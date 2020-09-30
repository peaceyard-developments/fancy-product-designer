<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('FPD_Labels_Products') ) {

	class FPD_Labels_Products {

		public static function get_labels() {

			return apply_filters('fpd_admin_labels_products',
				array (
				  'createProductFrom' => __( 'Create Product From', 'radykal' ),
				  'enterProductTitle' => __( 'Enter a product title', 'radykal' ),
				  'noEmpyProductTitle' => __( 'The product title can not be empty!', 'radykal' ),
				  'templatesLibrary' => __( 'Templates Library', 'radykal' ),
				  'templatesLibrarySubHeader' => __( 'Browse the large templates library and create ready-to-use products from our pre-made templates with just one click. We offer at least one product from each category for free. If you want to use the other premium products, you have to buy the whole category set.', 'radykal' ),
				  'categories' => __( 'Categories', 'radykal' ),
				  'buySet' => __( 'Buy Set', 'radykal' ),
				  'createProduct' => __( 'Create Product', 'radykal' ),
				  'new' => __( 'New', 'radykal' ),
				  'myTemplates' => __( 'My Templates', 'radykal' ),
				  'deleteTemplate' => __( 'Delete Template', 'radykal' ),
				  'deleteTemplateText' => __( 'Are you sure to delete the template?', 'radykal' ),
				  'demos' => __( 'Demos', 'radykal' ),
				  'demosSubHeader' => __( 'Download a demo and import it via the Products dashboard.', 'radykal' ),
				  'manageCategories' => __( 'Manage Categories', 'radykal' ),
				  'searchProducts' => __( 'Search Products...', 'radykal' ),
				  'search' => __( 'Search', 'radykal' ),
				  'download' => __( 'Download', 'radykal' ),
				  'enterCategoryTitle' => __( 'Enter a product title', 'radykal' ),
				  'noEmpyCategoryTitle' => __( 'The category title can not be empty!', 'radykal' ),
				  'selectProductForCat' => __( 'Please select a Product in the table first to assign the category!', 'radykal' ),
				  'deleteCategory' => __( 'Delete Category', 'radykal' ),
				  'deleteCategoryText' => __( 'Are you sure to delete the category?', 'radykal' ),
				  'editCategoryTitle' => __( 'Edit Title', 'radykal' ),
				  'showProductsInCategory' => __( 'Show only products in this category', 'radykal' ),
				  'addNewCategory' => __( 'Add New Category', 'radykal' ),
				  'closeSidebar' => __( 'Close Sidebar', 'radykal' ),
				  'noCategoriesCreated' => __( 'No categories created yet!', 'radykal' ),
				  'productOptions' => __( 'Product Options', 'radykal' ),
				  'set' => __( 'Set', 'radykal' ),
				  'stageWidth' => __( 'Canvas Width', 'radykal' ),
				  'stageWidth_desc' => __( 'For the best performance keep it under 4000px.', 'radykal' ),
				  'stageHeight' => __( 'Canvas Height', 'radykal' ),
				  'stageHeight_desc' => __( 'For the best performance keep it under 4000px.', 'radykal' ),
				  'layouts_product_id' => __( 'Layouts', 'radykal' ),
				  'enterViewTitle' => __( 'Enter a view title', 'radykal' ),
				  'noEmptyViewTitle' => __( 'The view title can not be empty!', 'radykal' ),
				  'productNoView' => __( 'The product does not contain any view!', 'radykal' ),
				  'enterTemplateTitle' => __( 'Enter a template title', 'radykal' ),
				  'noEmptyTemplateTitle' => __( 'The template title can not be empty!', 'radykal' ),
				  'deleteProduct' => __( 'Delete Product', 'radykal' ),
				  'deleteProductText' => __( 'Are you sure to delete the product?', 'radykal' ),
				  'deleteView' => __( 'Delete View', 'radykal' ),
				  'deleteViewText' => __( 'Are you sure to delete the view?', 'radykal' ),
				  'addView' => __( 'Add View', 'radykal' ),
				  'editProductTitle' => __( 'Edit Product Title', 'radykal' ),
				  'editProductOptions' => __( 'Edit Product Options', 'radykal' ),
				  'exportProduct' => __( 'Export Product', 'radykal' ),
				  'saveTemplate' => __( 'Save as Template', 'radykal' ),
				  'duplicateProduct' => __( 'Duplicate Product', 'radykal' ),
				  'editViewProductBuilder' => __( 'Edit View in Product Builder', 'radykal' ),
				  'editViewTitle' => __( 'Edit View Title', 'radykal' ),
				  'duplicateView' => __( 'Duplicate View', 'radykal' ),
				  'id' => __( 'ID', 'radykal' ),
				  'thumbnail' => __( 'Thumbnail', 'radykal' ),
				  'title' => __( 'Title', 'radykal' ),
				  'actions' => __( 'Actions', 'radykal' ),

				  //container
				  'addToMediaLibrary' => __( 'Imported images into Media Library?', 'radykal' ),
				  'addToMediaLibraryText' => __( 'The imported images will also be added to your WordPress Media Library.', 'radykal' ),
				  'yes' => __( 'Yes', 'radykal' ),
				  'no' => __( 'No', 'radykal' ),
				  'import' => __( 'Import', 'radykal' ),
				  'selectUser' => __( 'Select User', 'radykal' ),
				  'loadingProducts' => __( 'Loading Products...', 'radykal' ),
				  'selectThumbnail' => __( 'Select Thumbnail', 'radykal' ),
				  'loadingLayouts' => __( 'Loading Layouts...', 'radykal' ),
				  'loadingLibraryTemplates' => __( 'Loading Library Templates...', 'radykal' ),
				  'loadingTemplates' => __( 'Loading Templates...', 'radykal' ),
				  'creatingProduct' => __( 'Creating Product...', 'radykal' ),
				  'updatingProduct' => __( 'Updating Product...', 'radykal' ),
				  'deletingProduct' => __( 'Deleting Product...', 'radykal' ),
				  'creatingTemplate' => __( 'Creating Template...', 'radykal' ),
				  'deletingTemplate' => __( 'Deleting Template...', 'radykal' ),
				  'updatingView' => __( 'Updating View...', 'radykal' ),
				  'deletingView' => __( 'Deleting View...', 'radykal' ),
				  'loadingProductCategories' => __( 'Loading Product Categories...', 'radykal' ),
				  'creatingProductCategory' => __( 'Creating Product Category...', 'radykal' ),
				  'updatingProductCategory' => __( 'Updating Product Category...', 'radykal' ),
				  'deletingProductCategory' => __( 'Deleting Product Category...', 'radykal' ),
				  'noProductsCreated' => __( 'No products created yet!', 'radykal' ),
				)
			);

		}
	}

}

?>