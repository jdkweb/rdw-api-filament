<input type="radio" name="tabs" id="tab4"
       x-model="activeTab"
       value="4"
       class="hidden [&:checked+label]:bg-white [&:checked+label+div]:block">
<label for="tab4"
       x-on:click="location.href='/{{ config('rdw-api.rdw_api_folder') }}/{{ config('rdw-api.rdw_api_demo_slug') }}'"
       class="order-1 px-4 py-4 mr-1 rounded rounded-b-none cursor-pointer font-semibold bg-gray-200 transition">
    Post Full Form<span class="block text-center text-sm font-normal">Laravel</span>
</label>
<div class="order-last hidden p-4 bg-white w-full rounded rounded-tl-none transition">
</div>
