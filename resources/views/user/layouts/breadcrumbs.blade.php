<div class="pagetitle">
    <h1>{{ $title }}</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('user.dashboard')}}">Home</a></li>
        @foreach ($breadcrumbs as $breadcrumb)
        <li class="breadcrumb-item {{ $loop->last ? 'active' : '' }}">
          <a href="{{ $breadcrumb['url'] }}" class="{{ $loop->last ? 'active' : '' }}">
            {{ $breadcrumb['text'] }}
          </a>
        </li>
        @endforeach
      </ol>
    </nav>
</div>
