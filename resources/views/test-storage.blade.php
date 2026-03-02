@extends('layouts.app')

@section('title', 'Storage Test')

@section('content')
<div class="container">
    <h3>Storage Test</h3>
    
    <div class="row">
        <div class="col-md-6">
            <h5>Test Image URLs:</h5>
            <p><strong>Storage URL:</strong> {{ Storage::url('test.jpg') }}</p>
            <p><strong>Asset URL:</strong> {{ asset('storage/test.jpg') }}</p>
            <p><strong>Public Path:</strong> {{ public_path('storage') }}</p>
            <p><strong>Storage Path:</strong> {{ storage_path('app/public') }}</p>
        </div>
        <div class="col-md-6">
            <h5>Directory Check:</h5>
            <p><strong>Public/storage exists:</strong> {{ file_exists(public_path('storage')) ? 'Yes' : 'No' }}</p>
            <p><strong>Storage/app/public exists:</strong> {{ file_exists(storage_path('app/public')) ? 'Yes' : 'No' }}</p>
        </div>
    </div>

    @if($students->count() > 0)
        <h5>Sample Student Images:</h5>
        @foreach($students->take(3) as $student)
            <div class="card mb-3">
                <div class="card-body">
                    <h6>{{ $student->first_name }} {{ $student->last_name }}</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Photo Path:</strong> {{ $student->photo_path ?? 'None' }}</p>
                            @if($student->photo_path)
                                <p><strong>Photo URL:</strong> {{ Storage::url($student->photo_path) }}</p>
                                <p><strong>File exists:</strong> {{ Storage::exists($student->photo_path) ? 'Yes' : 'No' }}</p>
                                <img src="{{ Storage::url($student->photo_path) }}" style="width: 100px; height: 120px; object-fit: cover;" class="img-thumbnail">
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>Signature Path:</strong> {{ $student->signature_path ?? 'None' }}</p>
                            @if($student->signature_path)
                                <p><strong>Signature URL:</strong> {{ Storage::url($student->signature_path) }}</p>
                                <p><strong>File exists:</strong> {{ Storage::exists($student->signature_path) ? 'Yes' : 'No' }}</p>
                                <img src="{{ Storage::url($student->signature_path) }}" style="width: 100px; height: 50px; object-fit: cover;" class="img-thumbnail">
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection