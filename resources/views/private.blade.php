@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <private-chat :user="{{ Auth::user() }}" />
            </div>
        </div>
    </div>
@endsection

<script>
    import PrivateChat from "../js/components/PrivateChat";
    export default {
        components: { PrivateChat }
    }
</script>
