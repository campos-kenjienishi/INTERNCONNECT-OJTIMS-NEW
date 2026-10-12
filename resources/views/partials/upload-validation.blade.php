@php
    $uploadValidationFeedback = null;
    $uploadValidationSuccess = session('upload_success');
    $uploadValidationError = $errors->first('file') ?: session('upload_error');

    if ($uploadValidationSuccess) {
        $uploadValidationFeedback = [
            'type' => 'success',
            'title' => 'Upload successful',
            'message' => $uploadValidationSuccess,
        ];
    } elseif ($uploadValidationError) {
        $uploadValidationFeedback = [
            'type' => 'error',
            'title' => 'Upload rejected',
            'message' => $uploadValidationError,
        ];
    }
@endphp

<link rel="stylesheet" href="{{ vasset('css/components/upload-validation.css') }}">
<script>
    window.uploadValidationFeedback = @json($uploadValidationFeedback);
</script>
<script src="{{ vasset('js/components/upload-validation.js') }}" defer></script>
