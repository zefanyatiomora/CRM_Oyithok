<section class="content-header">
    <div class="container-fluid">
        @if (isset($breadcrumb) && isset($breadcrumb->title) && isset($breadcrumb->list))
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom"
             style="border-bottom: 3px solid #5C54AD;">
            
            <!-- Judul -->
            <h1 class="h3 fw-bold mb-0" style="color: #2C2C2C; letter-spacing: 0.5px;">
                {{ $breadcrumb->title }}
            </h1>

            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0 bg-transparent p-0">
                    @foreach ($breadcrumb->list as $key => $value)
                        @if ($loop->last)
                            <li class="breadcrumb-item active fw-semibold text-secondary" aria-current="page">
                                {{ is_numeric($key) ? $value : $key }}
                            </li>
                        @else
                            <li class="breadcrumb-item">
                                @if (is_string($value) && !empty($value))
                                    <a href="{{ $value }}" 
                                       class="fw-semibold text-decoration-none"
                                       style="color: #5C54AD;">
                                        {{ is_numeric($key) ? $value : $key }}
                                    </a>
                                @else
                                    {{ is_numeric($key) ? $value : $key }}
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ol>
            </nav>
        </div>
        @endif
    </div>
</section>
