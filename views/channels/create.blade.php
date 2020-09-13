<div class="row-fluid">
    <form
        role="form"
        id="form-widget"
        accept-charset="utf-8"
        method="POST"
        action="{{ current_url() }}">

        @csrf

        {!! $this->renderForm() !!}

    </form>
</div>

