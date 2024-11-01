/**
 * CLink Register Sidebar API Scripts
 *
 * Register sidebar API
 *
 */
( function( wp ) {
	
// Register	
var el = wp.element.createElement;
var Fragment = wp.element.Fragment;
var PluginSidebar = wp.editPost.PluginSidebar;
var PluginSidebarMoreMenuItem = wp.editPost.PluginSidebarMoreMenuItem;
var registerPlugin = wp.plugins.registerPlugin;
	

	
// CLink Icon in SVG format
var iconEl = el('svg', { width: 20, height: 20 },
  el('path', { d: "M5.335,4.066A5.823,5.823,0,0,1,8.9,4.851c0.485,1.708-1.275.723-2,.55a4.546,4.546,0,0,0-2,.039c-2.679.725-4.8,4.137-2.824,7.1a4.734,4.734,0,0,0,8.119-.51,5.607,5.607,0,0,0,.431-1.492c0.162-.88-0.423-2.873,1.1-1.845a5.263,5.263,0,0,1,0,2.747c-0.9,3.309-5,6.041-8.825,3.65A6.117,6.117,0,0,1,1.217,6.421,6.217,6.217,0,0,1,3.962,4.38Zm8.079,0c3.465-.053,5.546,1.555,6.354,4.121,1.6,5.092-4.267,9.942-8.589,6.947a1.19,1.19,0,0,1-.039-0.707,0.782,0.782,0,0,1,.353-0.275c0.55-.2,1.2.32,1.687,0.432a4.779,4.779,0,0,0,2.275-.118c3.2-1.024,4.626-5.693,1.647-8.046A4.733,4.733,0,0,0,9.767,8.227c-0.27.641-.16,2.942-0.628,3.179a0.778,0.778,0,0,1-.628,0,4.079,4.079,0,0,1-.118-3.022,5.836,5.836,0,0,1,3.569-3.964Z" } )
);

/**
 * CLink Component Render
 * 
 */
function wpclink_Component() {
    return el(
        Fragment,
        {},
        el(
            PluginSidebarMoreMenuItem,
            {
                target: 'sidebar-clink',
            },
            'CLink'
        ),
        el(
            PluginSidebar,
            {
                name: 'sidebar-clink',
                title: 'CLink',
            },		
             el( 'div',
                    { className: 'toolbar-loading-ajax' }
				),
			el( 'div',
                    { className: 'plugin-sidebar-content-identifier' }
                ),
			el( 'div',
                    { className: 'plugin-sidebar-content-type' }
                ),
			el( 'div',
                    { className: 'plugin-sidebar-content-links' }
                ),
			 wpclink_toolbox_init()
			  ),
		
    );
}
registerPlugin( 'plugin-clink', {
    icon: iconEl,
    render: wpclink_Component,
} );
} )( window.wp );

wp.domReady( () => {
	const { removeEditorPanel } = wp.data.dispatch('core/edit-post');
	
	//  Add to Taxonomy does not need to have catergories and tags
	if ( jQuery('#cl_taxonomy_permission').length ) {
	
		var cl_permission = jQuery('#cl_taxonomy_permission').val();
		if(cl_permission == 'AddToTaxonomy'){
		
			removeEditorPanel( 'taxonomy-panel-category' );
			removeEditorPanel( 'taxonomy-panel-post_tag' );
			
		}
	}
	
} );
