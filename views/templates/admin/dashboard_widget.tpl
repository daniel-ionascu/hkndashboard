<section id="hk-dashboard-stats" class="panel widget">
    <div class="panel-heading">
        <i class="icon-bar-chart"></i> {$widget_title|escape:'html':'UTF-8'}
    </div>

    <div class="panel-body">
        {if $data && count($data) > 0}
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Product Name' mod='hkndashboard'}</th>
                        {if $stats_type == 'views'}
                            <th class="text-center">{l s='Views' mod='hkndashboard'}</th>
                        {else}
                            <th class="text-center">{l s='Stock' mod='hkndashboard'}</th>
                        {/if}
                        <th class="text-center">{l s='Actions' mod='hkndashboard'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$data item=item}
                        <tr>
                            <td>
                                <strong>{$item.name|escape:'html':'UTF-8'}</strong>
                            </td>
                            <td class="text-center">
                                {if $stats_type == 'views'}
                                    <span class="badge badge-info">{$item.total_views|intval}</span>
                                {else}
                                    <span class="badge {if $item.physical_quantity <= 0}badge-danger{else}badge-warning{/if}">
                                        {$item.physical_quantity|intval}
                                    </span>
                                {/if}
                            </td>
                            <td class="text-center">
                                <a href="{$admin_product_link|escape:'html':'UTF-8'}&id_product={$item.id_product|intval}&updateproduct"
                                   class="btn btn-default btn-sm"
                                   title="{l s='Edit' mod='hkndashboard'}">
                                    <i class="icon-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
        {else}
            <div class="alert alert-info">
                <i class="icon-info-circle"></i>
                {if $stats_type == 'views'}
                    {l s='No product views found in the selected period.' mod='hkndashboard'}
                {else}
                    {l s='No low stock products found.' mod='hkndashboard'}
                {/if}
            </div>
        {/if}
    </div>
</section>

<style>
#hk-dashboard-stats {
    margin-bottom: 20px;
}

#hk-dashboard-stats .table {
    margin-bottom: 0;
}

#hk-dashboard-stats .badge {
    font-size: 12px;
    padding: 5px 10px;
}

#hk-dashboard-stats .panel-heading {
    font-weight: 600;
}
</style>
