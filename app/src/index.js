import { registerPlugin } from '@wordpress/plugins';
import { useState, useEffect } from '@wordpress/element';
import { PluginSidebar } from '@wordpress/edit-post';
import { Panel, PanelBody, PanelRow, FormTokenField } from '@wordpress/components';
import { image } from '@wordpress/icons';


const PluginSidebarEuCapacito = () => {
    const [selectedTags, setSelectedTags] = useState([]);
    const [allTags, setAllTags] = useState([]);

    useEffect( () => {
        const tags = [];
        fetch("https://wp.eucapacito.com.br/wp-json/wp/v2/tags?context=view&per_page=100&orderby=name&order=asc&hide_empty=true&_fields=id,name,count&_locale=user")
            .then( resp => resp.json())
            .then( data => {
                data.forEach( tag => {
                    tags.push(tag.name)
                })
            })
        setAllTags(tags)
    }, []);

    return (
        <PluginSidebar name="plugin-sidebar-eucapacito" title="Eu Capacito" icon={image}>
            <Panel>
                <PanelBody title="Todas as Tags" initialOpen={true}>
                    <PanelRow>
                        <FormTokenField value={selectedTags} suggestions={allTags} onChange={(token) => setSelectedTags(token)}/>
                    </PanelRow>
                    <PanelRow>
                        <ul>
                        {allTags.length > 0 &&
                            allTags.map( (tag) => (
                                <li>
                                    {tag}
                                </li>
                            ))}
                        </ul>
                    </PanelRow>
                </PanelBody>
            </Panel>
        </PluginSidebar>
    );
};

registerPlugin( 'plugin-sidebar-eucapacito', { render: PluginSidebarEuCapacito } );