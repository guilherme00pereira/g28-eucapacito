(function (wp) {
    const { registerPlugin } = wp.plugins;
    const { PluginDocumentSettingPanel } = wp.editPost;


    registerPlugin( 'misha-custom-panel', {
        render: function(){

            return (
                <PluginDocumentSettingPanel name="misha-seo" title="SEO" icon="chart-area">
                    content
                </PluginDocumentSettingPanel>
            )

        },
        icon: 'airplane' // or false if you do not need an icon
    } );
})(window.wp);