<?php
//
//use Illuminate\Database\Migrations\Migration;
//use Illuminate\Database\Schema\Blueprint;
//use Illuminate\Support\Facades\DB;
//use Illuminate\Support\Facades\Schema;
//use Illuminate\Support\Facades\Storage;
//use Illuminate\Support\Str;
//
//return new class extends Migration
//{
//    /**
//     * Run the migrations.
//     */
//    public function up(): void
//    {
//        // 1. Add the new media_path column
////        Schema::table('media', function (Blueprint $table) {
////            $table->string('media_path')->nullable()->after('media');
////        });
//
//        // 2. Make sure the target directory exists
//        Storage::disk('public')->makeDirectory('media_files');
//
//        // 3. Migrate existing records in chunks to avoid memory issues
//        DB::table('media')
//            ->select('id', 'media', 'file_name')
//            ->whereNotNull('media')
//            ->orderBy('id')
//            ->chunk(10, function ($rows) {
//                foreach ($rows as $row) {
//                    $rawMedia = $row->media;
//
//                    if (empty($rawMedia)) {
//                        continue;
//                    }
//
//                    // Strip the "data:image/png;base64," prefix if present
//                    $base64String = $rawMedia;
//                    $extension = 'png'; // default fallback
//
//                    if (preg_match('/^data:image\/(\w+);base64,/', $rawMedia, $matches)) {
//                        $extension = strtolower($matches[1]); // e.g. png, jpeg, jpg, webp
//                        $base64String = substr($rawMedia, strpos($rawMedia, ',') + 1);
//                    }
//
//                    // Decode
//                    $decoded = base64_decode($base64String, true);
//
//                    if ($decoded === false) {
//                        // Skip invalid base64 data instead of crashing the migration
//                        continue;
//                    }
//
//                    // Build a unique file name
//                    $fileName = $row->file_name
//                        ? pathinfo($row->file_name, PATHINFO_FILENAME).'_'.$row->id.'.'.$extension
//                        : 'media_'.$row->id.'_'.Str::random(8).'.'.$extension;
//
//                    $relativePath = 'media_files/'.$fileName;
//
//                    // Store on the public disk (storage/app/public/media_files)
//                    Storage::disk('public')->put($relativePath, $decoded);
//
//                    // Update the row with the new path
//                    DB::table('media')
//                        ->where('id', $row->id)
//                        ->update(['media_path' => $relativePath]);
//                }
//            });
//
//        // 4. Drop the old blob column
//        //        Schema::table('media', function (Blueprint $table) {
//        //            $table->dropColumn('media');
//        //        });
//    }
//
//    /**
//     * Reverse the migrations.
//     */
//    public function down(): void
//    {
//        // Re-add the blob column
////        Schema::table('media', function (Blueprint $table) {
////            $table->binary('media')->nullable()->after('media_path');
////        });
//
//        // Restore data from files back to base64
//        DB::table('media')
//            ->select('id', 'media_path')
//            ->whereNotNull('media_path')
//            ->orderBy('id')
//            ->chunk(100, function ($rows) {
//                foreach ($rows as $row) {
//                    if (! Storage::disk('public')->exists($row->media_path)) {
//                        continue;
//                    }
//
//                    $contents = Storage::disk('public')->get($row->media_path);
//                    $extension = pathinfo($row->media_path, PATHINFO_EXTENSION);
//                    $mime = $extension === 'jpg' ? 'jpeg' : $extension;
//                    $base64 = 'data:image/'.$mime.';base64,'.base64_encode($contents);
//
//                    DB::table('media')
//                        ->where('id', $row->id)
//                        ->update(['media' => $base64]);
//                }
//            });
//
//        // Drop the new column
//        Schema::table('media', function (Blueprint $table) {
//            $table->dropColumn('media_path');
//        });
//    }
//};
