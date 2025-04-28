@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <h6>Redirection vers le nouveau système de suivi...</h6>
                </div>
                <div class="card-body px-4 pt-4 pb-4">
                    <p>Vous allez être redirigé vers notre nouveau système de suivi en temps réel...</p>
                    <div class="d-flex justify-content-center my-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Redirect to the new tracking page after a brief delay
    setTimeout(() => {
        @if(auth()->user()->isClient())
            window.location.href = "{{ route('client.commands.track', ['command' => $command->id]) }}";
        @else
            window.location.href = "{{ route('livreur.commands.track', ['command' => $command->id]) }}";
        @endif
    }, 1500);
</script>
@endsection