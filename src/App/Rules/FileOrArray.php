<?php
namespace Amerhendy\Employment\App\Rules;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;
class FileOrArray implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // لو القيمة عبارة عن ملف مرفوع (UploadedFile)
        if ($value instanceof UploadedFile) {
            if (!$value->isValid()) {
                $fail(trans('JOBLANG::apply.errors.file'));
            } elseif ($value->getSize() > 8 * 1024 * 1024) {
                $fail(trans('JOBLANG::apply.errors.upload.Size'));
            } elseif ($value->getClientMimeType() !== 'application/pdf') {
                $fail(trans('JOBLANG::apply.errors.upload.NOtPDF'));
            }
        }

        // لو القيمة عبارة عن مصفوفة تحتوي على بيانات ملف
        elseif (is_array($value)) {
            if (
                !isset($value['name'], $value['size'], $value['type'], $value['lastModified']) ||
                !is_string($value['name']) ||
                !is_int($value['size']) ||
                !is_string($value['type']) ||
                !is_int($value['lastModified'])
            ) {
                $fail(trans('JOBLANG::apply.errors.file'));
                return;
            }

            if ($value['size'] > 8 * 1024 * 1024) {
                $fail(trans('JOBLANG::apply.errors.upload.Size'));
            }

            if ($value['type'] !== 'application/pdf') {
                $fail(trans('JOBLANG::apply.errors.upload.NOtPDF'));
            }
        }

        // لا ملف ولا مصفوفة
        else {
            $fail(trans('JOBLANG::apply.errors.upload.NOtPDF'));
        }
    }
}

