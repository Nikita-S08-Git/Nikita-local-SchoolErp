<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';

// Boot the app
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$request = Illuminate\Http\Request::capture();
$response = $kernel->handle($request);

use Illuminate\Support\Facades\Validator;

$rules = (new App\Http\Requests\StoreGuardianRequest())->rules();
$input = [
    'guardian_type' => 'guardian',
    'full_name' => 'Test Name',
    'mobile_number' => '9035466787',
];
$validator = Validator::make($input, $rules);
if ($validator->passes()) {
    echo "passes\n";
} else {
    echo "fails\n";
    print_r($validator->errors()->all());
}

$kernel->terminate($request, $response);
