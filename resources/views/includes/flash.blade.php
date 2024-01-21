<div id="flash-msg">
    @if (session()->has('message'))
        @php
            $flashMessage = session('message');
            $showConfirmation = !empty($confirmation) && $confirmation == "no" ? false : true;
        @endphp
        @if($showConfirmation)
            <div class="alert alert-{{key($flashMessage)}} alert-dismissible">
                <a href="javascript:void(0);" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                {{ $flashMessage[key($flashMessage)] }}
            </div>
        @endif
    @endif

    {{-- Show validation errors if any --}}
    @if (empty($e_type))
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif
</div>