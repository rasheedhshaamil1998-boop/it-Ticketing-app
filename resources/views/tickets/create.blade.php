<x-layouts.app title="Create Ticket - IT HELPDESK" heading="Create Ticket">
    <div class="content-card p-4">
        <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">
                <div class="col-lg-8">
                    <label class="form-label" for="subject">Ticket Subject</label>
                    <input class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required>
                    @error('subject')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-lg-4">
                    <label class="form-label" for="priority">Priority</label>
                    <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority" required>
                        @foreach ($priorities as $priority)
                            <option value="{{ $priority }}" @selected(old('priority') === $priority)>{{ $priority }}</option>
                        @endforeach
                    </select>
                    @error('priority')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="category">Category</label>
                    <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category }}" @selected(old('category') === $category)>{{ $category }}</option>
                        @endforeach
                    </select>
                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="location">Location</label>
                    <input class="form-control @error('location') is-invalid @enderror" id="location" name="location" value="{{ old('location') }}" required>
                    @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="department">Department</label>
                    <input class="form-control @error('department') is-invalid @enderror" id="department" name="department" value="{{ old('department', $department) }}" required>
                    @error('department')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="attachment">Attachment</label>
                    <input class="form-control @error('attachment') is-invalid @enderror" id="attachment" name="attachment" type="file" accept=".jpg,.jpeg,.png,.pdf">
                    <div class="form-text">JPG, JPEG, PNG, or PDF up to 5 MB.</div>
                    @error('attachment')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    <label class="form-label" for="description">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="7" required>{{ old('description') }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button class="btn btn-primary" type="submit"><i class="bi bi-send me-2"></i>Submit Ticket</button>
            </div>
        </form>
    </div>
</x-layouts.app>
