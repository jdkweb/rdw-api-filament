<div>
    <div class="w-full bg-gray-300">
        <div class="mx-auto pt-12 w-[80%] max-w[600px]"
             x-data="{activeTab: 1}"
             x-init="activeTab = new URLSearchParams(location.search).get('tab') ?? 1;
                     $watch('activeTab', (value) => {
                     if (history.pushState) {
                         let newurl = window.location.protocol + '//' + window.location.host + window.location.pathname + '?tab='+activeTab;
                         window.history.pushState({path:newurl},'',newurl);
             }})">
            <div class="flex flex-wrap tabs">
                <x-rdw_views::tab tabnumber="1" tabname="Async Full Form"/>
                <x-rdw_views::tab tabnumber="2" tabname="Async Small Form"/>
                <x-rdw_views::tab tabnumber="3" tabname="Async Alt Form"/>
                <x-rdw_views::form-tab/>
            </div>
            {!! $livewire_results ?? '' !!}
            @script
            <script>
                // Pretty Json
                $wire.on('getJsonResult', obj => {
                    const elementExist = setInterval(() => {
                        let elm = document.getElementById('code');
                        if (elm != null) {
                            elm.innerHTML = prettyPrintJson.toHtml(JSON.parse(obj.result));
                            clearInterval(elementExist);
                        }
                    }, 100);
                });
            </script>
            @endscript
        </div>
        <x-filament-actions::modals/>
    </div>
</div>
