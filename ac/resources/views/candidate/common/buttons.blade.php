<div class="fullwidth">
         @foreach($buttons as $button)
        <a class="pull-right btn btn-success" title="{!! $button['name'] !!}" href="{!! $button['href'] !!}">
        <i class="fa {!! $button['icon'] !!}"></i> {!! $button['name'] !!}</a>
        @endforeach


        </div>