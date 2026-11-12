<div class="row" id="client-list">
    @foreach ($clients as $client)
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="form-check form-check-md">
                            <input class="form-check-input" type="checkbox">
                        </div>
                        <div>
                            <a href="{{ route('admin.clients.show', $client->id) }}"
                                class="avatar avatar-xl avatar-rounded online border p-1 border-primary rounded-circle">
                                <img src="{{ $client->profile ? asset('storage/' . $client->profile) : 'https://via.placeholder.com/150' }}"
                                    class="img-fluid h-auto w-auto" alt="img">
                            </a>

                        </div>

                        <div class="dropdown">
                            <button class="btn btn-icon btn-sm rounded-circle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end p-3">
                                <li>
                                    <a class="dropdown-item rounded-1" href="">
                                        <i class="ti ti-edit me-1"></i>Edit
                                    </a>
                                </li>
                                <li>
                                    <form action="" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dropdown-item rounded-1 text-danger">
                                            <i class="ti ti-trash me-1"></i>Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="text-center mb-3">
                        <h6 class="mb-1">
                            <a href="">
                                {{ $client->first_name }} {{ $client->last_name }}
                            </a>
                        </h6>
                        <span class="badge bg-pink-transparent fs-10 fw-medium">{{ $client->status }}</span>
                    </div>

                    <div>
                        <p class="mb-2 text-truncate">Email: {{ $client->email }}</p>
                        <p class="mb-2 text-truncate">Phone: {{ $client->phone }}</p>
                    </div>

                    <div class="d-flex align-items-center justify-content-between border-top pt-3 mt-3">
                        <div>
                            <p class="mb-1 fs-12">Company</p>
                            <h6 class="fw-normal text-truncate">
                                {{ $client->company_name ?? 'N/A' }}
                            </h6>
                        </div>
                        <div class="icons-social d-flex align-items-center">
                            <a href="mailto:{{ $client->email }}" class="avatar avatar-rounded avatar-sm bg-light me-2">
                                <i class="ti ti-message"></i>
                            </a>
                            <a href="tel:{{ $client->phone }}" class="avatar avatar-rounded avatar-sm bg-light">
                                <i class="ti ti-phone"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
