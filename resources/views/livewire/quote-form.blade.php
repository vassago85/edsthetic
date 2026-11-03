@php
    $messages = collect($errors->all())->unique();
@endphp
<form id="quote" wire:submit="submit" novalidate>
    <div hidden aria-hidden="true">
        <input type="text" wire:model="extraFields.{{ $honeypotName }}" tabindex="-1" autocomplete="off">
        <input type="text" wire:model="extraFields.{{ $honeypotValidFrom }}" tabindex="-1" autocomplete="off">
    </div>
    <div class="row">
        <label>Name<input name="name" wire:model="name" autocomplete="name" required></label>
        <label>Phone or WhatsApp<input name="phone" wire:model="phone" type="tel" autocomplete="tel" required></label>
    </div>
    <div class="row">
        <label>Email <span class="opt">(optional)</span><input name="email" wire:model="email" type="email" autocomplete="email"></label>
        <label>Suburb<input name="suburb" wire:model="suburb" autocomplete="address-level2"></label>
    </div>
    <label>What do you need?
        <select name="type" wire:model="jobType">
            @foreach ($jobTypes as $type)
            <option value="{{ $type->value }}">{{ $type->getLabel() }}</option>
            @endforeach
        </select>
    </label>
    <label>Tell us about the job<textarea name="msg" wire:model="message" rows="4" placeholder="Which room, rough size, the finish you like, when you'd want it done"></textarea></label>
    <button class="btn btn-primary" type="submit" wire:loading.attr="disabled" wire:target="submit">Send quote request</button>
    <div id="status" aria-live="polite">
        @if ($messages->isNotEmpty())
        <div class="status err">
            @foreach ($messages as $error)
            <div>{{ $error }}</div>
            @endforeach
        </div>
        @endif
        @if ($sent)
        <div class="status">Quote request sent. We'll be in touch within {{ $callbackDays }} working days to book a site measure.</div>
        @endif
    </div>
</form>
