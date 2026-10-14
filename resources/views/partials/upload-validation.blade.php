@php
    $uploadValidationFeedback = null;
    $uploadValidationSuccess = session('upload_success');
    $renewalValidationSuccess = session('renewal_success');
    $uploadValidationError = $errors->first('file') ?: session('upload_error');
    $renewalValidationError = session('renewal_error');

    if ($renewalValidationSuccess) {
        $uploadValidationFeedback = [
            'type' => 'success',
            'title' => 'MOA renewed',
            'message' => $renewalValidationSuccess,
        ];
    } elseif ($uploadValidationSuccess) {
        $uploadValidationFeedback = [
            'type' => 'success',
            'title' => 'Upload successful',
            'message' => $uploadValidationSuccess,
        ];
    } elseif ($renewalValidationError) {
        $uploadValidationFeedback = [
            'type' => 'error',
            'title' => 'Renewal rejected',
            'message' => $renewalValidationError,
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
