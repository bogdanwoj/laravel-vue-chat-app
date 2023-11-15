@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h3>General chat room</h3>

           <chat :user="{{Auth::user()}}" />
        </div>
    </div>
</div>
@endsection
<script>
    import Chat from "../js/components/Chat";
    export default {
        components: {Chat}
    }
</script>
