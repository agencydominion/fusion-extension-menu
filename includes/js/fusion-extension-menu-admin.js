/**
 * WP Admin scripts for Menu extension
 */

//init menu
jQuery(document).ready(function() {
	jQuery('body').on('show.bs.modal', '#fsn_menu_modal', function(e) {
		var menuModal = jQuery(this);
		var selectLayoutElement = menuModal.find('[name="menu_layout"]');
		var selectedLayout = selectLayoutElement.val();
		menuModal.attr('data-layout', selectedLayout);
	});
});

//update menu function
jQuery(document).ready(function() {
	jQuery('body').on('change', 'select[name="menu_layout"]', function(e) {
		fsnUpdateMenu(e);
	});
});

function fsnUpdateMenu(event) {
	var selectLayoutElement = jQuery(event.target);
	var selectedLayout = selectLayoutElement.val();
	var menuModal = selectLayoutElement.closest('.modal');
	var currentLayout = menuModal.attr('data-layout');
	if (currentLayout != '' && currentLayout != selectedLayout) {
		var r = confirm(fsnExtMenuL10n.layout_change);
		if (r == true) {
			menuModal.attr('data-layout', selectedLayout);
			fsnUpdateMenuLayout(menuModal);
		} else {
			selectLayoutElement.find('option[value="'+ currentLayout +'"]').prop('selected', true);
		}
	} else {
		menuModal.attr('data-layout', selectedLayout);
		fsnUpdateMenuLayout(menuModal);
	}
}

//update menu layout
function fsnUpdateMenuLayout(menuModal) {
	var postID = jQuery('input#post_ID').val();
	var menuLayout = menuModal.find('[name="menu_layout"]').val();

	var data = {
		action: 'menu_load_layout',
		menu_layout: menuLayout,
		post_id: postID,
		security: fsnExtMenuJS.fsnEditMenuNonce
	};
	jQuery.post(ajaxurl, data, function(response) {
		if (response == '-1') {
			alert(fsnExtMenuL10n.error);
			return false;
		}

		menuModal.find('.tab-pane .form-group.menu-layout').remove();
		if (response !== null) {
			menuModal.find('.tab-pane').each(function() {
				var tabPane = jQuery(this);
				if (tabPane.attr('data-section-id') == 'general') {
					tabPane.find('.form-group').first().after('<div class="layout-fields"></div>');
				} else {
					tabPane.prepend('<div class="layout-fields"></div>');
				}
			});
			for(i=0; i < response.length; i++) {
				menuModal.find('.tab-pane[data-section-id="'+ response[i].section +'"] .layout-fields').append(response[i].output);
			}
			menuModal.find('.tab-pane').each(function() {
				var tabPane = jQuery(this);
				tabPane.find('.menu-layout').first().unwrap();
				tabPane.find('.layout-fields:empty').remove();
				//toggle panel tabs visibility
				var tabPaneId = tabPane.attr('id');
				if (tabPane.is(':empty')) {
					menuModal.find('a[data-toggle="tab"][href="#'+ tabPaneId +'"]').parent('li').hide();
				} else {
					menuModal.find('a[data-toggle="tab"][href="#'+ tabPaneId +'"]').parent('li').show();
				}
			});
		}
		var modalSelector = menuModal;
		//reinit tinyMCE
		if (jQuery('#fsncontent').length > 0) {
			//make compatable with TinyMCE 4 which is used starting with WordPress 3.9
			if(tinymce.majorVersion === "4") {
				tinymce.execCommand('mceRemoveEditor', true, 'fsncontent');
            } else {
				tinymce.execCommand("mceRemoveControl", true, 'fsncontent');
            }
			var $element = jQuery('#fsncontent');
	        var qt, textfield_id = $element.attr("id"),
	            content = '';

	        window.tinyMCEPreInit.mceInit[textfield_id] = _.extend({}, tinyMCEPreInit.mceInit['content']);

	        if(_.isUndefined(tinyMCEPreInit.qtInit[textfield_id])) {
	            window.tinyMCEPreInit.qtInit[textfield_id] = _.extend({}, tinyMCEPreInit.qtInit['replycontent'], {id: textfield_id})
	        }
	        //$element.val($content_holder.val());
	        qt = quicktags( window.tinyMCEPreInit.qtInit[textfield_id] );
	        QTags._buttonsInit();
	        //make compatable with TinyMCE 4 which is used starting with WordPress 3.9
	        if(tinymce.majorVersion === "4") tinymce.execCommand( 'mceAddEditor', true, textfield_id );
	        window.switchEditors.go(textfield_id, 'tmce');
	        //focus on this RTE
	        tinyMCE.get('fsncontent').focus();
			//destroy tinyMCE
			modalSelector.on('hidden.bs.modal', function() {
				//make compatable with TinyMCE 4 which is used starting with WordPress 3.9
				if(tinymce.majorVersion === "4") {
					tinymce.execCommand('mceRemoveEditor', true, 'fsncontent');
                } else {
					tinymce.execCommand("mceRemoveControl", true, 'fsncontent');
                }
			});
		}
		//initialize color pickers
		jQuery('.fsn-color-picker').wpColorPicker();
		//set dependencies
		setDependencies(modalSelector);
		//trigger item added event
		jQuery('body').trigger('fsnMenuUpdated');
	});
}

//For select2 fields inside menu items
jQuery(document).ready(function() {
	jQuery('body').on('fsnMenuUpdated', function(e) {
		fsnInitPostSelect();
	});
});
