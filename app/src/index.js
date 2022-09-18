import { registerPlugin } from '@wordpress/plugins';
import { useState, useEffect } from '@wordpress/element';
import { PluginSidebar } from '@wordpress/edit-post';
import { Panel, PanelBody, PanelRow, FormTokenField, Button, Spinner } from '@wordpress/components';
import { tag } from '@wordpress/icons';

const baseUrl = "https://wp.eucapacito.com.br/wp-json"


const PluginSidebarEuCapacito = () => {
    const [selectedTags, setSelectedTags] = useState([]);
    const [allTags, setAllTags] = useState([]);
    const [loading, setLoading] = useState(false);

    useEffect( () => {
        const urlParams = new URLSearchParams(window.location.search);
        const postID = urlParams.get('post');
        const tags = [];
        const postTags = [];

        fetch(baseUrl + "/wp/v2/tags?post=" + postID + "&context=view&per_page=100&orderby=name&order=asc&_fields=id,name,count", {cache: "no-cache"})
            .then( resp => resp.json())
            .then( data => {
                data.forEach( tag => {
                    postTags.push(tag.name)
                })
                setSelectedTags(postTags)
            })

        fetch(baseUrl + "/wp/v2/tags?context=view&per_page=999&orderby=name&order=asc&hide_empty=true&_fields=id,name,count")
            .then( resp => resp.json())
            .then( data => {
                data.forEach( tag => {
                    tags.push(tag.name)
                })
                setAllTags(tags)
            })
    }, []);

    const updateTokens = (e) => {
        const value = e.target.innerHTML
        setSelectedTags([...selectedTags, value])
    }

    const saveTokens = async () => {
        setLoading(true)
        const urlParams = new URLSearchParams(window.location.search);
        const postID = urlParams.get('post');
        const raw = await fetch(baseUrl + '/eucapacito/v1/post-tags', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: postID, tags: selectedTags })
        });
        const content = await raw.json()
        console.log(content)
        setLoading(false)
    }

    return (
        <PluginSidebar name="plugin-sidebar-eucapacito" title="Eu Capacito" icon={tag}>
            <Panel>
                <PanelBody title="Todas as Tags" initialOpen={true}>
                    <PanelRow>
                        <FormTokenField value={selectedTags} suggestions={allTags} onChange={(token) => setSelectedTags(token)}/>
                    </PanelRow>
                    <PanelRow>
                        <Button variant="primary" className="is-primary" onClick={saveTokens}>Salvar</Button>
                        {loading && <Spinner />}
                    </PanelRow>
                    <PanelRow>
                        <ul>
                        {
                            allTags.map( (tag) => <li style={styles.tagItem} onClick={updateTokens}>{tag}</li>)
                        }
                        </ul>
                    </PanelRow>
                </PanelBody>
            </Panel>
        </PluginSidebar>
    );
};

registerPlugin( 'plugin-sidebar-eucapacito', { render: PluginSidebarEuCapacito } );

const styles = {
    tagItem: {
        cursor: "pointer"
    }
}