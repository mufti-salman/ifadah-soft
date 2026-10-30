<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class UserPhotoController extends Controller
{
    /**
     * Current institution ID.
     *
     * Never return users from all institutions
     * when no institution is selected.
     */
    private function institutionId(): int
    {
        $authUser = auth()->user();

        $institutionId = session('active_institution_id')
            ?: $authUser?->institution_id;

        abort_unless(
            $institutionId,
            403,
            'প্রতিষ্ঠান নির্বাচন করা হয়নি।'
        );

        return (int) $institutionId;
    }

    /**
     * Institution-scoped user query.
     */
    private function usersQuery()
    {
        return User::query()
            ->where(
                'institution_id',
                $this->institutionId()
            );
    }

    /**
     * Display user photo management page.
     */
    public function index()
    {
        $users = $this->usersQuery()
            ->orderBy('institution_user_id')
            ->paginate(15);

        return view(
            'admin.user-photos.index',
            compact('users')
        );
    }

    /**
     * Search user by user code.
     */
    public function search(Request $request)
    {
        $validated = $request->validate([
            'code' => [
                'required',
                'string',
                'max:100',
            ],
        ]);

        $code = trim($validated['code']);

        $user = $this->usersQuery()
            ->where(function ($query) use ($code) {
                $query
                    ->where('institution_user_id', $code)
                    ->orWhere('custom_id', $code);
            })
            ->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'এই User Code-এর কোনো ব্যবহারকারী পাওয়া যায়নি।',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'user' => [
                'id' => $user->id,
                'code' => $user->institution_user_id
                    ?: $user->custom_id,
                'name' => $user->name_bn
                    ?: $user->name
                    ?: $user->username,
                'photo' => $user->photo
                    && Storage::disk('public')->exists($user->photo)
                        ? Storage::disk('public')->url($user->photo)
                        : null,
            ],
        ]);
    }

    /**
     * Upload or replace user photo.
     */
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'integer',
            ],
            'photo' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
        ], [
            'user_id.required' => 'প্রথমে User Code দিয়ে ব্যবহারকারী খুঁজুন।',
            'photo.required' => 'আপলোড করার জন্য একটি ছবি নির্বাচন করুন।',
            'photo.image' => 'সঠিক ছবির ফাইল নির্বাচন করুন।',
            'photo.mimes' => 'শুধু JPG, JPEG, PNG অথবা WEBP ছবি আপলোড করা যাবে।',
            'photo.max' => 'ছবির সাইজ সর্বোচ্চ ৫ MB হতে পারবে।',
        ]);

        $user = $this->usersQuery()
            ->whereKey($validated['user_id'])
            ->firstOrFail();

        $disk = Storage::disk('public');
        $oldPhoto = $user->photo;

        /*
         * Store the new file first.
         * Keep the old photo until the database update succeeds.
         */
        $newPhoto = $request->file('photo')
            ->store('users/photos', 'public');

        try {
            $user->photo = $newPhoto;
            $user->save();
        } catch (\Throwable $e) {
            if ($disk->exists($newPhoto)) {
                $disk->delete($newPhoto);
            }

            throw $e;
        }

        /*
         * Delete the previous photo only after saving
         * the new photo successfully.
         */
        if (
            $oldPhoto
            && $oldPhoto !== $newPhoto
            && $disk->exists($oldPhoto)
        ) {
            $disk->delete($oldPhoto);
        }

        return response()->json([
            'success' => true,
            'message' => 'প্রোফাইল ছবি সফলভাবে আপলোড হয়েছে।',
            'photo' => $disk->url($newPhoto),
        ]);
    }

    /**
     * Remove user's current photo.
     */
    public function destroy($userId)
    {
        $user = $this->usersQuery()
            ->whereKey($userId)
            ->firstOrFail();

        if (!$user->photo) {
            return response()->json([
                'success' => false,
                'message' => 'এই ব্যবহারকারীর কোনো ছবি নেই।',
            ], 422);
        }

        $oldPhoto = $user->photo;

        $user->photo = null;
        $user->save();

        if (Storage::disk('public')->exists($oldPhoto)) {
            Storage::disk('public')->delete($oldPhoto);
        }

        return response()->json([
            'success' => true,
            'message' => 'ব্যবহারকারীর ছবি মুছে ফেলা হয়েছে।',
        ]);
    }

    /**
     * Download institution users' photos as ZIP.
     */
    public function downloadAll()
    {
        if (!class_exists(ZipArchive::class)) {
            return back()->with(
                'error',
                'সার্ভারে ZIP extension চালু নেই।'
            );
        }

        $disk = Storage::disk('public');

        $users = $this->usersQuery()
            ->whereNotNull('photo')
            ->orderBy('institution_user_id')
            ->get([
                'id',
                'institution_user_id',
                'custom_id',
                'name',
                'name_bn',
                'username',
                'photo',
            ]);

        $photos = $users->filter(function ($user) use ($disk) {
            return $user->photo
                && $disk->exists($user->photo);
        });

        if ($photos->isEmpty()) {
            return back()->with(
                'error',
                'ডাউনলোড করার মতো কোনো ব্যবহারকারীর ছবি পাওয়া যায়নি।'
            );
        }

        $zipPath = tempnam(
            sys_get_temp_dir(),
            'user_photos_'
        );

        if ($zipPath === false) {
            return back()->with(
                'error',
                'ZIP ফাইল তৈরি করা যায়নি।'
            );
        }

        $zip = new ZipArchive();

        if ($zip->open($zipPath, ZipArchive::OVERWRITE) !== true) {
            @unlink($zipPath);

            return back()->with(
                'error',
                'ZIP ফাইল খোলা যায়নি।'
            );
        }

        $added = 0;
        $usedNames = [];

        foreach ($photos as $user) {
            $sourcePath = $disk->path($user->photo);

            if (!is_file($sourcePath)) {
                continue;
            }

            $code = $user->institution_user_id
                ?: $user->custom_id
                ?: $user->id;

            $name = $user->name_bn
                ?: $user->name
                ?: $user->username
                ?: 'user';

            /*
             * Sanitize archive filename.
             */
            $baseName = preg_replace(
                '/[^\pL\pN_-]+/u',
                '_',
                $code . '_' . $name
            );

            $extension = strtolower(
                pathinfo($user->photo, PATHINFO_EXTENSION)
            );

            if (!in_array(
                $extension,
                ['jpg', 'jpeg', 'png', 'webp'],
                true
            )) {
                $extension = 'jpg';
            }

            $fileName = $baseName . '.' . $extension;

            /*
             * Prevent duplicate filenames inside the ZIP.
             */
            $counter = 1;
            $originalName = $fileName;

            while (isset($usedNames[strtolower($fileName)])) {
                $fileName = pathinfo(
                    $originalName,
                    PATHINFO_FILENAME
                ) . '_' . $counter . '.' . $extension;

                $counter++;
            }

            $usedNames[strtolower($fileName)] = true;

            if ($zip->addFile($sourcePath, $fileName)) {
                $added++;
            }
        }

        $zip->close();

        if ($added === 0) {
            @unlink($zipPath);

            return back()->with(
                'error',
                'ZIP ফাইলে কোনো ছবি যোগ করা যায়নি।'
            );
        }

        return response()
            ->download(
                $zipPath,
                'user-photos-' . date('Y-m-d') . '.zip'
            )
            ->deleteFileAfterSend(true);
    }
}