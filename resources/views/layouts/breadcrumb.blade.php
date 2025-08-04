<section class="content-header">
    <div class="container-fluid">
        @if (isset($breadcrumb) && isset($breadcrumb->title) && isset($breadcrumb->list))
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>{{ $breadcrumb->title }}</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    @foreach ($breadcrumb->list as $key => $value)
                        @if ($loop->last)
                            <li class="breadcrumb-item active">{{ is_numeric($key) ? $value : $key }}</li>
                        @else
                            <li class="breadcrumb-item">
                                @if (is_string($value) && !empty($value))
                                    <a href="{{ $value }}">{{ is_numeric($key) ? $value : $key }}</a>
                                @else
                                    {{ is_numeric($key) ? $value : $key }}
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ol>
            </div>
        </div>
        @endif
    </div><!-- /.container-fluid -->
</section>
