<input type="radio" name="tabs" id="tab{{ $tabnumber }}"
       x-model="activeTab"
       value="{{ $tabnumber }}"
       class="hidden [&:checked+label]:bg-white [&:checked+label+div]:block">
<label for="tab{{ $tabnumber }}"
       wire:click="clear()"
       class="order-1 px-4 py-4 mr-1 rounded rounded-b-none cursor-pointer font-semibold bg-gray-200 transition">
       {{ $tabname }}<span class="block text-center text-sm font-normal">Laravel/Filament</span>
</label>
<div class="order-last hidden p-4 bg-white w-full rounded rounded-tl-none transition">
    <form wire:submit="handleForm('exampleForm{{$tabnumber}}')">
        @csrf
        <div class="w-full relative">
            {{ $this->{'exampleForm'.$tabnumber} }}
            <button type="submit" class="m-12 mt-0 px-5 py-3 w-fit bg-blue-500 text-white rounded">
                Submit
            </button>
            <div wire:loading>
                <div class="absolute -mt-6 h-8 w-8 border-2 border-blue-600 border-t-transparent rounded-[50%] animate-spin"></div>
            </div>
        </div>
    </form>
</div>
