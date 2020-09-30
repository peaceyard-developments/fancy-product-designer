<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('FPD_Settings_Automated_Export') ) {

	class FPD_Settings_Automated_Export {

		public static function get_options() {

			return apply_filters('fpd_automated_export_settins', array(

				'ae-general' => array(

					array(
						'title' => __('Output Details', 'radykal'),
						'type' => 'section-title',
						'id' => 'file-output-section',
					),

					array(
						'title' 	=> __( 'Output File', 'radykal' ),
						'description' 		=> __( 'Set the output file that will you or your customers will receive.', 'radykal' ),
						'id' 		=> 'fpd_ae_output_file',
						'default'	=> 'pdf',
						'type' 		=> 'select',
						'css'		=> 'width: 300px',
						'options'   => self::get_export_types()
					),

					array(
						'title' => __( 'Image DPI', 'radykal' ),
						'description' 		=> 'Enter the image DPI for PNG or JPEG output.',
						'id' 		=> 'fpd_ae_image_dpi',
						'css' 		=> 'width: 100%;',
						'type' 		=> 'number',
						'default'	=> 300
					),

					array(
						'title' => __('File Receiving', 'radykal'),
						'type' => 'section-title',
						'id' => 'file-receiving-section',
						'description' => class_exists( 'WooCommerce' ) ? __( 'In WooCommerce the customer will receive the file(s) when the order is paid/completed.', 'radykal') : ''
					),

					array(
						'title' 	=> __( 'Download Link in E-Mail (Recommended)', 'radykal' ),
						'description'	 => __( 'A download link will be added in the mail. ', 'radykal' ),
						'id' 		=> 'fpd_ae_email_download_link',
						'default'	=> 'yes',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'E-Mail Attachment', 'radykal' ),
						'description'	 => __( 'The file(s) will be sent as attachment(s) in the E-Mail. Depending on the files size and the amount of files that need to generated might take a while and your server will reach its <a href="http://php.net/manual/en/function.set-time-limit.php" target="_blank">maximum execution time</a>. So test if your common order process is convenient for this method.', 'radykal' ),
						'id' 		=> 'fpd_ae_email_attachment',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 		=> __( 'Cloud', 'radykal' ),
						'description'	=> __( 'Choose a cloud provider to store the print-ready file when the order is received.', 'radykal' ),
						'id' 			=> 'fpd_ae_cloud',
						'default'		=> 'none',
						'type' 			=> 'radio',
						'options'   	=> array(
							'none' => __('None', 'radykal'),
							'dropbox' => __('Dropbox', 'radykal'),
							's3' => __('AWS S3', 'radykal')
						),
						'relations' => array(
							'none' => array(
								'fpd_ae_dropbox_access_token' => false,
								'fpd_ae_s3_access_key' => false,
								'fpd_ae_s3_access_secret' => false,
								'fpd_ae_s3_region' => false,
								'fpd_ae_s3_bucket' => false,
								'fpd_ae_s3_root_dir' => false,
							),
							'dropbox' => array(
								'fpd_ae_dropbox_access_token' => true,
								'fpd_ae_s3_access_key' => false,
								'fpd_ae_s3_access_secret' => false,
								'fpd_ae_s3_region' => false,
								'fpd_ae_s3_bucket' => false,
								'fpd_ae_s3_root_dir' => false,
							),
							's3' => array(
								'fpd_ae_dropbox_access_token' => false,
								'fpd_ae_s3_access_key' => true,
								'fpd_ae_s3_access_secret' => true,
								'fpd_ae_s3_region' => true,
								'fpd_ae_s3_bucket' => true,
								'fpd_ae_s3_root_dir' => true,
							),
						)
					),

					array(
						'title' 		=> __( 'Dropbox Access Token', 'radykal' ),
						'description'	=> 'Enter your Dropbox Access Token.',
						'id' 			=> 'fpd_ae_dropbox_access_token',
						'type' 			=> 'password',
						'default'		=> ''
					),

					array(
						'title' 		=> __( 'S3 Access Key', 'radykal' ),
						'description' 	=> 'Enter your S3 Access Key.',
						'id' 			=> 'fpd_ae_s3_access_key',
						'type' 			=> 'text',
						'default'		=> ''
					),

					array(
						'title' 		=> __( 'S3 Access Secret', 'radykal' ),
						'description' 	=> 'Enter your S3 Access Secret.',
						'id' 			=> 'fpd_ae_s3_access_secret',
						'type' 			=> 'password',
						'default'		=> ''
					),

					array(
						'title' 		=> __( 'S3 Region', 'radykal' ),
						'description' 	=> 'Select your S3 region.',
						'id' 			=> 'fpd_ae_s3_region',
						'type' 			=> 'select',
						'default'		=> 'us-east-2',
						'options'		=> array(
							"af-south-1" => "af-south-1",
							"ap-east-1" => "ap-east-1",
							"ap-northeast-1" => "ap-northeast-1",
							"ap-northeast-2" => "ap-northeast-2",
							"ap-northeast-3" => "ap-northeast-3",
							"ap-south-1" => "ap-south-1",
							"ap-southeast-1" => "ap-southeast-1",
							"ap-southeast-2" => "ap-southeast-2",
							"ca-central-1" => "ca-central-1",
							"cn-north-1" => "cn-north-1",
							"cn-northwest-1" => "cn-northwest-1",
							"eu-central-1" => "eu-central-1",
							"eu-north-1" => "eu-north-1",
							"eu-south-1" => "eu-south-1",
							"eu-west-1" => "eu-west-1",
							"eu-west-2" => "eu-west-2",
							"eu-west-3" => "eu-west-3",
							"me-south-1" => "me-south-1",
							"sa-east-1" => "sa-east-1",
							"us-east-1" => "us-east-1",
							"us-east-2" => "us-east-2",
							"us-gov-east-1" => "us-gov-east-1",
							"us-gov-west-1" => "us-gov-west-1",
							"us-west-1" => "us-west-1",
							"us-west-2" => "us-west-2"
						)
					),

					array(
						'title' 		=> __( 'S3 Bucket Name', 'radykal' ),
						'description' 	=> 'Enter your Bucket name.',
						'id' 			=> 'fpd_ae_s3_bucket',
						'type' 			=> 'text',
						'default'		=> ''
					),

					array(
						'title' 		=> __( 'S3 Root Directory', 'radykal' ),
						'description' 	=> 'Enter a name without slashes for the root directory where the print-ready files will be stored.',
						'id' 			=> 'fpd_ae_s3_root_dir',
						'type' 			=> 'text',
						'default'		=> 'fpd-print-ready-files'
					),

					array(
						'title' => __('Recipients', 'radykal'),
						'type' => 'section-title',
						'id' => 'recipients-section'
					),

					array(
						'title' 	=> __( 'Administrator', 'radykal' ),
						'description'	 => __( 'The administrator will receive the file when a new order is made.', 'radykal' ),
						'id' 		=> 'fpd_ae_recipient_admin',
						'default'	=> 'yes',
						'type' 		=> 'checkbox',
					),

					array(
						'title' 	=> __( 'Customer', 'radykal' ),
						'description'	 => __( 'Only in WooCommerce. The customer will receive the file when the WooCommerce order is completed/paid.', 'radykal' ),
						'id' 		=> 'fpd_ae_recipient_customer',
						'default'	=> 'no',
						'type' 		=> 'checkbox',
					),

					array(
						'title' => __('ADMIN solution', 'radykal'),
						'type' => 'section-title',
						'id' => 'admin-section'
					),

					array(
						'title' => __( 'API Key', 'radykal' ),
						'description' 		=> 'Enter the ADMIN API key. You can find it in the Site Configurations of a connected website in ADMIN.',
						'id' 		=> 'fpd_ae_admin_api_key',
						'css' 		=> 'width: 100%;',
						'type' 		=> 'text',
						'default'	=> '',
						'value' => ''
					),

				)

			));

		}

		public static function get_export_types() {

			return array(
				'pdf' => 'PDF',
				'jpeg' => 'JPEG',
				'png' => 'PNG',
				'zip_pdf_fonts' => __('Archive containing PDF and used fonts', 'radykal'),
				'zip_pdf_custom_images' => __('Archive containing PDF and custom images', 'radykal')
			);

		}

		public static function get_recipients() {

			return array(
				'admin' => __('Administrator', 'radykal'),
				'customer' =>  __('Customer', 'radykal')
			);

		}

	}

}