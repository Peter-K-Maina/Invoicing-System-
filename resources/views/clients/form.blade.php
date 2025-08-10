{{-- resources/views/clients/form.blade.php --}}

<div class="mb-3">
    <label for="name" class="form-label">Full Name</label>
    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $client->name ?? '') }}" required>
</div>

<div class="mb-3">
    <label for="email" class="form-label">Email Address</label>
    <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $client->email ?? '') }}">
</div>

<div class="mb-3">
    <label for="phone" class="form-label">Phone Number</label>
    <input type="text" class="form-control" name="phone" id="phone" value="{{ old('phone', $client->phone ?? '') }}">
</div>

<div class="mb-3">
    <label for="company" class="form-label">Company</label>
    <input type="text" class="form-control" name="company" id="company" value="{{ old('company', $client->company ?? '') }}">
</div>

<div class="mb-3">
    <label for="address" class="form-label">Address</label>
    <textarea class="form-control" name="address" id="address" rows="2">{{ old('address', $client->address ?? '') }}</textarea>
</div>
