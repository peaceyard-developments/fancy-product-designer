<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Labels_UI_Composer') ) {

	class FPD_Labels_UI_Composer {

		public static function get_labels() {

			return apply_filters('fpd_admin_labels_ui_composer',
				array (
				  'enterUiName' => __( 'Enter a UI name', 'radykal' ),
				  'resetUi' => __( 'Reset UI', 'radykal' ),
				  'resetUiText' => __( 'Are you sure to reset the UI?', 'radykal' ),
				  'deleteUi' => __( 'Delete UI', 'radykal' ),
				  'deleteUiText' => __( 'Are you sure to delete UI?', 'radykal' ),
				  'saveUi' => __( 'Save UI', 'radykal' ),
				  'saveNewUi' => __( 'Save As New', 'radykal' ),
				  'resetToDefault' => __( 'Reset To Default', 'radykal' ),
				  'delete' => __( 'Delete', 'radykal' ),
				  'userInterface' => __( 'User Interface', 'radykal' ),
				  'layout' => __( 'Layout', 'radykal' ),
				  'modules' => __( 'Modules', 'radykal' ),
				  'actions' => __( 'Actions', 'radykal' ),
				  'toolbar' => __( 'Toolbar', 'radykal' ),
				  'colors' => __( 'Colors', 'radykal' ),
				  'customCss' => __( 'Custom CSS', 'radykal' ),
				  'guidedTour' => __( 'Guided Tour', 'radykal' ),
				  'mainBar' => __( 'Main Bar', 'radykal' ),
				  'topBar' => __( 'Top Bar', 'radykal' ),
				  'sidebBarLeft' => __( 'Side Bar Left', 'radykal' ),
				  'sidebBarRight' => __( 'Side Bar Right', 'radykal' ),
				  'dynamicDialog' => __( 'Dynamic Dialog', 'radykal' ),
				  'offCanvasLeft' => __( 'Off-Canvas Left', 'radykal' ),
				  'offCanvasRight' => __( 'Off-Canvas Right', 'radykal' ),
				  'tabsSide' => __( 'Tabs Side', 'radykal' ),
				  'tabsTop' => __( 'Tabs Top', 'radykal' ),
				  'dimensions' => __( 'Dimensions', 'radykal' ),
				  'canvasWidth' => __( 'Canvas Width', 'radykal' ),
				  'canvasHeight' => __( 'Canvas Height', 'radykal' ),
				  'canvasSizeInfo' => __( 'For the best performance keep it under 4000px.', 'radykal' ),
				  'misc' => __( 'Miscellaneous', 'radykal' ),
				  'imageGridColumns' => __( 'Image Grid Columns', 'radykal' ),
				  'one' => __( 'One', 'radykal' ),
				  'two' => __( 'Two', 'radykal' ),
				  'three' => __( 'Three', 'radykal' ),
				  'four' => __( 'Four', 'radykal' ),
				  'five' => __( 'Five', 'radykal' ),
				  'initialActiveModule' => __( 'Initial Active Module', 'radykal' ),
				  'none' => __( 'None', 'radykal' ),
				  'containerShadow' => __( 'Container Shadow', 'radykal' ),
				  'shadow' => __( 'Shadow', 'radykal' ),
				  'noShadow' => __( 'No Shadow', 'radykal' ),
				  'viewSelectionPosition' => __( 'View Selection Position', 'radykal' ),
				  'insideTop' => __( 'Inside Top', 'radykal' ),
				  'insideRight' => __( 'Inside Right', 'radykal' ),
				  'insideBottom' => __( 'Inside Bottom', 'radykal' ),
				  'insideLeft' => __( 'Inside Left', 'radykal' ),
				  'outside' => __( 'Outside', 'radykal' ),
				  'selectedModules' => __( 'Your Selected Modules', 'radykal' ),
				  'dropModules' => __( 'Drop Modules Here', 'radykal' ),
				  'selectedModulesInfo' => __( 'These modules will be visible in your main navigation. Double-click on an item to remove it.', 'radykal' ),
				  'availableModules' => __( 'Available Modules', 'radykal' ),
				  'availableModulesInfo' => __( 'Drag desired modules to dropzone.', 'radykal' ),
				  'selectedActions' => __( 'Your Selected Actions', 'radykal' ),
				  'dropActions' => __( 'Drop Actions Here', 'radykal' ),
				  'selectedActionsInfo' => __( 'Double-click on an item to remove it.', 'radykal' ),
				  'availableActions' => __( 'Available Actions', 'radykal' ),
				  'availableActionsInfo' => __( 'Drag desired actions to dropzone.', 'radykal' ),
				  'alignment' => __( 'Alignment', 'radykal' ),
				  'topActions' => __( 'Top Actions', 'radykal' ),
				  'left' => __( 'Left', 'radykal' ),
				  'center' => __( 'Center', 'radykal' ),
				  'rightActions' => __( 'Right Actions', 'radykal' ),
				  'top' => __( 'Top', 'radykal' ),
				  'bottomActions' => __( 'Bottom Actions', 'radykal' ),
				  'leftActions' => __( 'Left Actions', 'radykal' ),
				  'excludeTools' => __( 'Exclude Tools', 'radykal' ),
				  'type' => __( 'Type', 'radykal' ),
				  'colorTheme' => __( 'Color Theme', 'radykal' ),
				  'white' => __( 'White', 'radykal' ),
				  'dark' => __( 'Dark', 'radykal' ),
				  'selectedColor' => __( 'Element Selected', 'radykal' ),
				  'boundingBoxColor' => __( 'Bounding Box', 'radykal' ),
				  'outOfBoundaryColor' => __( 'Out Of Bounding Box', 'radykal' ),
				  'cornerIconColor' => __( 'Corner Icon', 'radykal' ),
				  'primary_color' => __( 'Primary', 'radykal' ),
				  'secondary_color' => __( 'Secondary', 'radykal' ),
				  'updatePreview' => __( 'Update Preview', 'radykal' ),
				  'addStep' => __( 'ADD STEP', 'radykal' ),
				  'run' => __( 'RUN', 'radykal' ),

				  //container
				  'loadingUiLayouts' => __( 'Loading UI Layouts...', 'radykal' ),
				  'loadingUiData' => __( 'Loading UI Data...', 'radykal' ),
				  'creatingUi' => __( 'Creating UI...', 'radykal' ),
				  'updatingUi' => __( 'Updating UI...', 'radykal' ),
				  'deletingUi' => __( 'Deleting UI...', 'radykal' ),
				)
			);

		}
	}

}

?>