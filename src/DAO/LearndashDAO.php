<?php

namespace G28\Eucapacito\DAO;

use LDLMS_DB;

class LearndashDAO
{

    public static function getUserProgress( $userId, $courseId )
    {
        global $wpdb;
        $steps          = [];
        $progress       = learndash_user_get_course_progress( $userId, $courseId )['lessons'];
        foreach($progress as $k => $v) {
            $steps[] = [
                'id'        => $k,
                'status'    => '0'
            ];
        }
        $sql            = $wpdb->prepare( 'SELECT * FROM ' . esc_sql( LDLMS_DB::get_table_name( 'user_activity' ) ) . ' WHERE
                             course_id=%d AND user_id=%d AND activity_completed > 0', $courseId, $userId );
        $activities     = $wpdb->get_results( $sql, ARRAY_A );
        if( !empty( $activities ) ) {
            foreach ($activities as $activity) {
                $key = array_search($activity['post_id'], array_column($steps, 'id'));
                $steps[$key]['status'] = isset($activity['activity_completed']) && $activity['activity_completed'] > 0 ? "completed" : '0';
            }
        }
        return $steps;
    }

    public static function genCertificate( $cert_args = array() ) {

        $cert_args_defaults = array(
            'cert_id'       => 0,     // The certificate Post ID.
            'post_id'       => 0,     // The Course/Quiz Post ID.
            'user_id'       => 0,     // The User ID for the Certificate.
            'lang'          => 'eng', // The default language.

            'filename'      => '',
            'filename_url'  => '',
            'filename_type' => 'title',

            'pdf_title'     => '',
            'ratio'         => 1.25,

            /*
            I: send the file inline to the browser (default).
            D: send to the browser and force a file download with the name given by name.
            F: save to a local server file with the name given by name.
            S: return the document as a string (name is ignored).
            FI: equivalent to F + I option
            FD: equivalent to F + D option
            E: return the document as base64 mime multi-part email attachment (RFC 2045)
            */
        );
        $cert_args = shortcode_atts( $cert_args_defaults, $cert_args );

        // Just to ensure we have valid IDs.
        $cert_args['cert_id'] = absint( $cert_args['cert_id'] );
        $cert_args['post_id'] = absint( $cert_args['post_id'] );
        $cert_args['user_id'] = absint( $cert_args['user_id'] );

        if ( empty( $cert_args['cert_id'] ) ) {
            if ( isset( $_GET['id'] ) ) {
                $cert_args['cert_id'] = absint( $_GET['id'] );
            } else {
                $cert_args['cert_id'] = get_the_id();
            }
        }

        if ( empty( $cert_args['user_id'] ) ) {
            if ( isset( $_GET['user'] ) ) {
                $cert_args['user_id'] = absint( $_GET['user'] );
            } elseif ( isset( $_GET['user_id'] ) ) {
                $cert_args['user_id'] = absint( $_GET['user_id'] );
            }
        }

        $cert_args['cert_post'] = get_post( $cert_args['cert_id'] );
        if ( ( ! $cert_args['cert_post'] ) || ( ! is_a( $cert_args['cert_post'], 'WP_Post' ) ) || ( learndash_get_post_type_slug( 'certificate' ) !== $cert_args['cert_post']->post_type ) ) {
            wp_die( esc_html__( 'Certificate Post does not exist.', 'learndash' ) );
        }

        $cert_args['post_post'] = get_post( $cert_args['post_id'] );
        if ( ( ! $cert_args['post_post'] ) || ( ! is_a( $cert_args['post_post'], 'WP_Post' ) ) ) {
            wp_die( esc_html__( 'Awarded Post does not exist.', 'learndash' ) );
        }

        $cert_args['user'] = get_user_by( 'ID', $cert_args['user_id'] );
        if ( ( ! $cert_args['user'] ) || ( ! is_a( $cert_args['user'], 'WP_User' ) ) ) {
            wp_die( esc_html__( 'User does not exist.', 'learndash' ) );
        }

        // Start config override section.

        // Language codes in TCPDF are 3 character eng, fra, ger, etc.
        /**
         * We check for cert_lang=xxx first since it may need to be different than
         * lang=yyy.
         */
        $config_lang_tmp = '';
        if ( ( isset( $_GET['cert_lang'] ) ) && ( ! empty( $_GET['cert_lang'] ) ) ) {
            $config_lang_tmp = substr( esc_attr( $_GET['cert_lang'] ), 0, 3 );
        } elseif ( ( isset( $_GET['lang'] ) ) && ( ! empty( $_GET['lang'] ) ) ) {
            $config_lang_tmp = substr( esc_attr( $_GET['lang'] ), 0, 3 );
        }

        if ( ( ! empty( $config_lang_tmp ) ) && ( strlen( $config_lang_tmp ) == 3 ) ) {
            $ld_cert_lang_dir = LEARNDASH_LMS_LIBRARY_DIR . '/tcpdf/config/lang';
            $lang_files       = array_diff( scandir( $ld_cert_lang_dir ), array( '..', '.' ) );
            if ( ( ! empty( $lang_files ) ) && ( is_array( $lang_files ) ) && ( in_array( $config_lang_tmp, $lang_files, true ) ) && ( file_exists( $ld_cert_lang_dir . '/' . $config_lang_tmp . '.php' ) ) ) {
                $cert_args['lang'] = $config_lang_tmp;
            }
        }

        $target_post_id             = 0;
        $cert_args['filename_type'] = 'title';

        $logo_file         = '';
        $logo_enable       = '';
        $subsetting_enable = '';
        $filters           = '';
        $header_enable     = '';
        $footer_enable     = '';
        $monospaced_font   = '';
        $font              = '';
        $font_size         = '';
        $destination       = 'I';
        $destination_type  = 'U';

        ob_start();

        $cert_args['cert_title'] = $cert_args['cert_post']->post_title;
        $cert_args['cert_title'] = wp_strip_all_tags( $cert_args['cert_title'] );

        /** This filter is documented in https://developer.wordpress.org/reference/hooks/document_title_separator/ */
        $sep = apply_filters( 'document_title_separator', '-' ); // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedHooknameFound -- WP Core Hook

        /**
         * Filters username of the user to be used in creating certificate PDF.
         *
         * @param string $user_name User display name.
         * @param int    $user_id   User ID.
         * @param int    $cert_id   Certificate post ID.
         */
        $learndash_pdf_username = apply_filters( 'learndash_pdf_username', $cert_args['user']->display_name, $cert_args['user_id'], $cert_args['cert_id'] );
        if ( ! empty( $learndash_pdf_username ) ) {
            if ( ! empty( $cert_args['pdf_title'] ) ) {
                $cert_args['pdf_title'] .= " $sep ";
            }
            $cert_args['pdf_title'] .= $learndash_pdf_username;
        }

        $cert_for_post_title = get_the_title( $cert_args['post_id'] );
        wp_strip_all_tags( $cert_for_post_title );
        if ( ! empty( $cert_for_post_title ) ) {
            if ( ! empty( $cert_args['pdf_title'] ) ) {
                $cert_args['pdf_title'] .= " $sep ";
            }
            $cert_args['pdf_title'] .= $cert_for_post_title;
        }

        if ( ! empty( $cert_args['pdf_title'] ) ) {
            $cert_args['pdf_title'] .= " $sep ";
        }
        $cert_args['pdf_title'] .= $cert_args['cert_title'];

        if ( ! empty( $cert_args['pdf_title'] ) ) {
            $cert_args['pdf_title'] .= " $sep ";
        }
        $cert_args['pdf_title'] .= get_bloginfo( 'name', 'display' );

        $cert_args['cert_permalink']  = get_permalink( $cert_args['cert_post']->ID );
        $cert_args['pdf_author_name'] = $cert_args['user']->display_name;

        $tags_array                = array();
        $cert_args['pdf_keywords'] = '';
        $tags_data                 = wp_get_post_tags( $cert_args['cert_post']->ID );

        if ( $tags_data ) {
            foreach ( $tags_data as $val ) {
                $tags_array[] = $val->name;
            }
            $cert_args['pdf_keywords'] = implode( ' ', $tags_array );
        }

        if ( ! empty( $_GET['file'] ) ) {
            $cert_args['filename_type'] = $_GET['file'];
        }

        if ( 'title' == $cert_args['filename_type'] && 0 == $target_post_id ) {
            $filename = sanitize_file_name( str_replace( " $sep ", "$sep", $cert_args['pdf_title'] ) );
            /**
             * Filters the file name of the certificate pdf.
             *
             * @param string $filename Name of the pdf file.
             * @param int    $cert_id Certificate post ID.
             */
            $filename = apply_filters( 'learndash_pdf_filename', $filename, $cert_args['cert_id'] );

        } else {
            $filename = $cert_args['cert_id'] . '.pdf';
        }
        $filename = basename( $filename );
        $filename = substr( $filename, 0, 255 );
        $filename = sanitize_file_name( $filename );

        $chached_filename = '';

        $query_string_params_supported = array( 'font', 'monospaced', 'fontsize', 'subsetting', 'ratio', 'header', 'logo', 'logo_file', 'logo_width', 'footer', 'destination', 'destination_type' );

        $query_string_params_allowed = array( 'destination', 'destination_type' );

        /**
         * Filter for allowed PDF Certificate parameters.
         *
         * @since 3.4.1
         *
         * @param array $query_string_params_allowed   Array of allowed query string params.
         * @param array $query_string_params_supported Array of supported query string params.
         */
        $query_string_params_allowed = apply_filters( 'learndash_certificate_query_string_params_allowed', $query_string_params_allowed, $query_string_params_supported );

        if ( ! is_array( $query_string_params_allowed ) ) {
            $query_string_params_allowed = array();
        }

        $query_string_params_allowed = array_intersect( $query_string_params_allowed, $query_string_params_supported );

        if ( in_array( 'font', $query_string_params_allowed, true ) ) {
            if ( ! empty( $_GET['font'] ) ) {
                $font = esc_html( $_GET['font'] );
            }
        }

        if ( in_array( 'monospaced', $query_string_params_allowed, true ) ) {
            if ( ! empty( $_GET['monospaced'] ) ) {
                $monospaced_font = esc_html( $_GET['monospaced'] );
            }
        }

        if ( in_array( 'fontsize', $query_string_params_allowed, true ) ) {
            if ( ! empty( $_GET['fontsize'] ) ) {
                $font_size = intval( $_GET['fontsize'] );
            }
        }

        if ( in_array( 'subsetting', $query_string_params_allowed, true ) ) {
            if ( ! empty( $_GET['subsetting'] ) && ( 1 == $_GET['subsetting'] || 0 == $_GET['subsetting'] ) ) {
                $subsetting_enable = $_GET['subsetting'];
            }
        }

        if ( 1 == $subsetting_enable ) {
            $subsetting = 'true';
        } else {
            $subsetting = 'false';
        }

        if ( in_array( 'ratio', $query_string_params_allowed, true ) ) {
            if ( ! empty( $_GET['ratio'] ) ) {
                $cert_args['ratio'] = floatval( $_GET['ratio'] );
            }
        }

        if ( in_array( 'header', $query_string_params_allowed, true ) ) {
            if ( ! empty( $_GET['header'] ) ) {
                $header_enable = $_GET['header'];
            }
        }

        if ( in_array( 'logo', $query_string_params_allowed, true ) ) {
            if ( ! empty( $_GET['logo'] ) ) {
                $logo_enable = $_GET['logo'];
            }
        }

        if ( in_array( 'logo_file', $query_string_params_allowed, true ) ) {
            if ( ! empty( $_GET['logo_file'] ) ) {
                $logo_file = esc_html( $_GET['logo_file'] );
            }
        }

        if ( in_array( 'logo_width', $query_string_params_allowed, true ) ) {
            if ( ! empty( $_GET['logo_width'] ) ) {
                $logo_width = intval( $_GET['logo_width'] );
            }
        }

        if ( in_array( 'footer', $query_string_params_allowed, true ) ) {
            if ( ! empty( $_GET['footer'] ) ) {
                $footer_enable = $_GET['footer'];
            }
        }

        if ( in_array( 'destination', $query_string_params_allowed, true ) ) {
            if ( ( isset( $_GET['destination'] ) ) && ( ! empty( $_GET['destination'] ) ) ) {
                if ( 'F' === $_GET['destination'] ) {
                    $destination = 'F';
                } else {
                    $destination = 'I';
                }
            } else {
                if ( 0 != $target_post_id ) {
                    $destination = 'F';
                } else {
                    $destination = 'I';
                }
            }
        }

        if ( in_array( 'destination_type', $query_string_params_allowed, true ) ) {
            if ( 'F' === $destination ) {
                if ( ( isset( $_GET['destination_type'] ) ) && ( ! empty( $_GET['destination_type'] ) ) ) {
                    if ( 'F' === $_GET['destination_type'] ) {
                        $destination_type = 'F';
                    } else {
                        $destination_type = 'U';
                    }
                }
            }
        }

        if ( 'F' === $destination ) {
            if ( ( defined( 'LEARNDASH_UPLOADS_BASE_URL' ) ) && ( ! empty( LEARNDASH_UPLOADS_BASE_URL ) ) ) {
                $cert_args['filename_url'] = LEARNDASH_UPLOADS_BASE_URL . '/certificates/' . $filename;
            }

            if ( ( defined( 'LEARNDASH_UPLOADS_BASE_DIR' ) ) && ( ! empty( LEARNDASH_UPLOADS_BASE_DIR ) ) && ( file_exists( LEARNDASH_UPLOADS_BASE_DIR ) ) && ( is_writable( LEARNDASH_UPLOADS_BASE_DIR ) ) ) {
                $ld_upload_certificates_dir = trailingslashit( LEARNDASH_UPLOADS_BASE_DIR ) . 'certificates';
                if ( ! file_exists( $ld_upload_certificates_dir ) ) {
                    if ( wp_mkdir_p( $ld_upload_certificates_dir ) !== false ) {
                        // To prevent security browsing add an index.php file.
                        learndash_put_directory_index_file( trailingslashit( $ld_upload_certificates_dir ) . 'index.php' );
                    }
                }
                $filename = trailingslashit( $ld_upload_certificates_dir ) . $filename;
            }
        }

        /**
         * Start Cert post content processing.
         */
        if ( ! defined( 'LEARNDASH_TCPDF_LEGACY_LD322' ) ) {
            $use_LD322_define = apply_filters( 'learndash_tcpdf_legacy_ld322', true, $cert_args ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

            /**
             * Define LearnDash LMS - Set to enable legacy TCPDF processing logic.
             *
             * LD 3.2.0 includes an upgrade of the TCPDF library for generating PDF
             * Certificates. The newer TCPDF library incldues some improvements which
             * cause the rendering to not match the prior version of the library. This
             * define if set to `true` will enable legacy logic in the new library.
             *
             * @since 3.2.2
             *
             * @var bool true When enabling legacy logic.
             */
            define( 'LEARNDASH_TCPDF_LEGACY_LD322', $use_LD322_define ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
        }
        $cert_content = $cert_args['cert_post']->post_content;

        // Delete shortcode for POST2PDF Converter.
        $cert_content = preg_replace( '|\[pdf[^\]]*?\].*?\[/pdf\]|i', '', $cert_content );
        $cert_content = do_shortcode( $cert_content );

        // Convert relative image path to absolute image path.
        $cert_content = preg_replace( "/<img([^>]*?)src=['\"]((?!(http:\/\/|https:\/\/|\/))[^'\"]+?)['\"]([^>]*?)>/i", '<img$1src="' . site_url() . '/$2"$4>', $cert_content );

        // Set image align to center.
        $cert_content = preg_replace_callback( "/(<img[^>]*?class=['\"][^'\"]*?aligncenter[^'\"]*?['\"][^>]*?>)/i", 'learndash_post2pdf_conv_image_align_center', $cert_content );

        // Add width and height into image tag.
        $cert_content = preg_replace_callback( "/(<img[^>]*?src=['\"]((http:\/\/|https:\/\/|\/)[^'\"]*?(jpg|jpeg|gif|png))['\"])([^>]*?>)/i", 'learndash_post2pdf_conv_img_size', $cert_content );

        if ( ( ! defined( 'LEARNDASH_TCPDF_LEGACY_LD322' ) ) || ( true !== LEARNDASH_TCPDF_LEGACY_LD322 ) ) {
            $cert_content = wpautop( $cert_content );
        }

        // For other sourcecode.
        $cert_content = preg_replace( '/<pre[^>]*?><code[^>]*?>(.*?)<\/code><\/pre>/is', '<pre style="word-wrap:break-word; color: #406040; background-color: #F1F1F1; border: 1px solid #9F9F9F;">$1</pre>', $cert_content );

        // For blockquote.
        $cert_content = preg_replace( '/<blockquote[^>]*?>(.*?)<\/blockquote>/is', '<blockquote style="color: #406040;">$1</blockquote>', $cert_content );

        $cert_content = '<br/><br/>' . $cert_content;

        /**
         * If the $font variable is not empty we use it to replace all font
         * definitions. This only affects inline styles within the structure
         * of the certificate content HTML elements.
         */
        if ( ! empty( $font ) ) {
            $cert_content = preg_replace( '/(<[^>]*?font-family[^:]*?:)([^;]*?;[^>]*?>)/is', '$1' . $font . ',$2', $cert_content );
        }

        if ( ( defined( 'LEARNDASH_TCPDF_LEGACY_LD322' ) ) && ( true === LEARNDASH_TCPDF_LEGACY_LD322 ) ) {
            $cert_content = preg_replace( '/\n/', '<br/>', $cert_content ); // "\n" should be treated as a next line.
        }

        /**
         * Filters whether to include certificate CSS styles in certificate content or not.
         *
         * @param boolean $include_certificate_styles Whether to include certificate styles.
         * @param int     $cert_id                   Certificate post ID.
         */
        if ( apply_filters( 'learndash_certificate_styles', true, $cert_args['cert_id'] ) ) {
            $certificate_styles = LearnDash_Settings_Section::get_section_setting( 'LearnDash_Settings_Certificates_Styles', 'styles' );
            $certificate_styles = preg_replace( '/<style[^>]*?>(.*?)<\/style>/is', '$1', $certificate_styles );
            if ( ! empty( $certificate_styles ) ) {
                $cert_content = '<style>' . $certificate_styles . '</style>' . $cert_content;
            }
        }

        /**
         * Filters certificate content after all processing.
         *
         * @since 3.2.0
         *
         * @param string $cert_content Certificate post content HTML/TEXT.
         * @param int    $cert_id      Certificate post ID.
         */
        $cert_content = apply_filters( 'learndash_certificate_content', $cert_content, $cert_args['cert_id'] );

        /**
         * Build the PDF Certificate using TCPDF.
         */
        if ( ! class_exists( 'TCPDF' ) ) {
            require_once LEARNDASH_LMS_LIBRARY_DIR . '/tcpdf/config/lang/' . $cert_args['lang'] . '.php';
            require_once LEARNDASH_LMS_LIBRARY_DIR . '/tcpdf/tcpdf.php';
        }

        $learndash_certificate_options = get_post_meta( $cert_args['cert_post']->ID, 'learndash_certificate_options', true );
        if ( ! is_array( $learndash_certificate_options ) ) {
            $learndash_certificate_options = array( $learndash_certificate_options );
        }

        if ( ! isset( $learndash_certificate_options['pdf_page_format'] ) ) {
            $learndash_certificate_options['pdf_page_format'] = PDF_PAGE_FORMAT;
        }

        if ( ! isset( $learndash_certificate_options['pdf_page_orientation'] ) ) {
            $learndash_certificate_options['pdf_page_orientation'] = PDF_PAGE_ORIENTATION;
        }

        // Create a new object.
        $tcpdf_params = array(
            'orientation' => $learndash_certificate_options['pdf_page_orientation'],
            'unit'        => PDF_UNIT,
            'format'      => $learndash_certificate_options['pdf_page_format'],
            'unicode'     => true,
            'encoding'    => 'UTF-8',
            'diskcache'   => false,
            'pdfa'        => false,
            'margins'     => array(
                'top'    => PDF_MARGIN_TOP,
                'right'  => PDF_MARGIN_RIGHT,
                'bottom' => PDF_MARGIN_BOTTOM,
                'left'   => PDF_MARGIN_LEFT,
            ),
        );

        /**
         * Filters certificate tcpdf paramaters.
         *
         * @since 2.4.7
         *
         * @param array $tcpdf_params An array of tcpdf parameters.
         * @param int   $cert_id      Certificate post ID.
         */
        $tcpdf_params = apply_filters( 'learndash_certificate_params', $tcpdf_params, $cert_args['cert_id'] );

        $pdf = new TCPDF(
            $tcpdf_params['orientation'],
            $tcpdf_params['unit'],
            $tcpdf_params['format'],
            $tcpdf_params['unicode'],
            $tcpdf_params['encoding'],
            $tcpdf_params['diskcache'],
            $tcpdf_params['pdfa']
        );

        // Added to let external manipulate the $pdf instance.
        /**
         * Fires after creating certificate `TCPDF` class object.
         *
         * @since 2.4.7
         *
         * @param TCPDF $pdf     `TCPDF` class instance.
         * @param int   $cert_id Certificate post ID.
         */
        do_action( 'learndash_certification_created', $pdf, $cert_args['cert_id'] );

        // Set document information.

        /**
         * Filters the value of pdf creator.
         *
         * @param string $pdf_creator The name of the PDF creator.
         * @param TCPDF  $pdf         `TCPDF` class instance.
         * @param int    $cert_id     Certificate post ID.
         */
        $pdf->SetCreator( apply_filters( 'learndash_pdf_creator', PDF_CREATOR, $pdf, $cert_args['cert_id'] ) );

        /**
         * Filters the name of the pdf author.
         *
         * @param string $pdf_author_name PDF author name.
         * @param TCPDF  $pdf             `TCPDF` class instance.
         * @param int    $cert_id         Certificate post ID.
         */
        $pdf->SetAuthor( apply_filters( 'learndash_pdf_author', $cert_args['pdf_author_name'], $pdf, $cert_args['cert_id'] ) );

        /**
         * Filters the title of the pdf.
         *
         * @param string $pdf_title PDF title.
         * @param TCPDF  $pdf       `TCPDF` class instance.
         * @param int    $cert_id   Certificate post ID.
         */
        $pdf->SetTitle( apply_filters( 'learndash_pdf_title', $cert_args['pdf_title'], $pdf, $cert_args['cert_id'] ) );

        /**
         * Filters the subject of the pdf.
         *
         * @param string $pdf_subject PDF subject
         * @param TCPDF  $pdf         `TCPDF` class instance.
         * @param int    $cert_id     Certificate post ID.
         */
        $pdf->SetSubject( apply_filters( 'learndash_pdf_subject', wp_strip_all_tags( get_the_category_list( ',', '', $cert_args['cert_id'] ) ), $pdf, $cert_args['cert_id'] ) );

        /**
         * Filters the pdf keywords.
         *
         * @param string $pdf_keywords PDF keywords.
         * @param TCPDF  $pdf          `TCPDF` class instance.
         * @param int    $cert_id      Certificate post ID.
         */
        $pdf->SetKeywords( apply_filters( 'learndash_pdf_keywords', $cert_args['pdf_keywords'], $pdf, $cert_args['cert_id'] ) );

        // Set header data.
        if ( mb_strlen( $cert_args['cert_title'], 'UTF-8' ) < 42 ) {
            $header_title = $cert_args['cert_title'];
        } else {
            $header_title = mb_substr( $cert_args['cert_title'], 0, 42, 'UTF-8' ) . '...';
        }

        if ( 1 == $header_enable ) {
            if ( 1 == $logo_enable && $logo_file ) {
                $pdf->SetHeaderData( $logo_file, $logo_width, $header_title, 'by ' . $cert_args['pdf_author_name'] . ' - ' . $cert_args['cert_permalink'] );
            } else {
                $pdf->SetHeaderData( '', 0, $header_title, 'by ' . $cert_args['pdf_author_name'] . ' - ' . $cert_args['cert_permalink'] );
            }
        }

        // Set header and footer fonts.
        if ( 1 == $header_enable ) {
            $pdf->setHeaderFont( array( $font, '', PDF_FONT_SIZE_MAIN ) );
        }

        if ( 1 == $footer_enable ) {
            $pdf->setFooterFont( array( $font, '', PDF_FONT_SIZE_DATA ) );
        }

        // Remove header/footer.
        if ( 0 == $header_enable ) {
            $pdf->setPrintHeader( false );
        }

        if ( 0 == $header_enable ) {
            $pdf->setPrintFooter( false );
        }

        // Set default monospaced font.
        $pdf->SetDefaultMonospacedFont( $monospaced_font );

        // Set margins.
        $pdf->SetMargins( $tcpdf_params['margins']['left'], $tcpdf_params['margins']['top'], $tcpdf_params['margins']['right'] );

        if ( 1 == $header_enable ) {
            $pdf->SetHeaderMargin( PDF_MARGIN_HEADER );
        }

        if ( 1 == $footer_enable ) {
            $pdf->SetFooterMargin( PDF_MARGIN_FOOTER );
        }

        // Set auto page breaks.
        $pdf->SetAutoPageBreak( true, $tcpdf_params['margins']['bottom'] );

        // Set image scale factor.
        if ( ! empty( $cert_args['ratio'] ) ) {
            $pdf->setImageScale( $cert_args['ratio'] );
        }

        // Set some language-dependent strings.
        //if ( isset( $l ) && ! empty( $l ) ) {
        //	$pdf->setLanguageArray( $l );
        //}

        // Set fontsubsetting mode.
        $pdf->setFontSubsetting( $subsetting );

        // Set font.
        if ( ( ! empty( $font ) ) && ( ! empty( $font_size ) ) ) {
            $pdf->SetFont( $font, '', $font_size, true );
        }

        // Add a page.
        $pdf->AddPage();

        // Added to let external manipulate the $pdf instance.
        /**
         * Fires after setting certificate pdf data.
         *
         * @since 2.4.7
         *
         * @param TCPDF $pdf     `TCPDF` class instance.
         * @param int   $post_id Post ID.
         */
        do_action( 'learndash_certification_after', $pdf, $cert_args['cert_id'] );

        // get featured image.
        $img_file = learndash_get_thumb_path( $cert_args['cert_id'] );

        // Only print image if it exists.
        if ( '' != $img_file ) {
            /**
             * Fires when thumbnail image processing starts.
             *
             * @since 3.3.0
             *
             * @param string $img_file  Thumbnail image file.
             * @param array  $cert_args Array of Certificate args.
             * @param TCPDF  $pdf       `TCPDF` class instance.
             */
            do_action( 'learndash_certification_thumbnail_processing_start', $img_file, $cert_args, $pdf );

            // Print BG image.
            $pdf->setPrintHeader( false );

            // get the current page break margin.
            $b_margin = $pdf->getBreakMargin();

            // get current auto-page-break mode.
            $auto_page_break = $pdf->getAutoPageBreak();

            // disable auto-page-break.
            $pdf->SetAutoPageBreak( false, 0 );

            // Get width and height of page for dynamic adjustments.
            $page_h = $pdf->getPageHeight();
            $page_w = $pdf->getPageWidth();

            /**
             * Fires before the thumbnail image is added to the PDF.
             *
             * @since 3.3.0
             *
             * @param string $img_file  Thumbnail image file.
             * @param array  $cert_args Array of Certificate args.
             * @param TCPDF  $pdf       `TCPDF` class instance.
             */
            do_action( 'learndash_certification_thumbnail_before', $img_file, $cert_args, $pdf );

            // Print the Background.
            $pdf->Image( $img_file, '0', '0', $page_w, $page_h, '', '', '', false, 300, '', false, false, 0, false, false, false, false, array() );

            /**
             * Fires after the thumbnail image is added to the PDF.
             *
             * @since 3.3.0
             *
             * @param string $img_file  Thumbnail image file.
             * @param array  $cert_args Array of Certificate args.
             * @param TCPDF  $pdf       `TCPDF` class instance.
             */
            do_action( 'learndash_certification_thumbnail_after', $img_file, $cert_args, $pdf );

            // restore auto-page-break status.
            $pdf->SetAutoPageBreak( $auto_page_break, $b_margin );

            // set the starting point for the page content.
            $pdf->setPageMark();

            /**
             * Fires when thumbnail image processing starts.
             *
             * @since 3.3.0
             *
             * @param string $img_file  Thumbnail image file.
             * @param array  $cert_args Array of Certificate args.
             * @param TCPDF  $pdf       `TCPDF` class instance.
             */
            do_action( 'learndash_certification_thumbnail_processing_end', $img_file, $cert_args, $pdf );
        }

        /**
         * Fires before the certificate content is added to the PDF.
         *
         * @since 3.3.0
         *
         * @param TCPDF  $pdf       `TCPDF` class instance.
         * @param array  $cert_args Array of certificate args.
         */
        do_action( 'learndash_certification_content_write_cell_before', $pdf, $cert_args );

        $pdf_cell_args = array(
            'w'           => 0,
            'h'           => 0,
            'x'           => '',
            'y'           => '',
            'content'     => $cert_content,
            'border'      => 0,
            'ln'          => 1,
            'fill'        => 0,
            'reseth'      => true,
            'align'       => '',
            'autopadding' => true,
        );

        /**
         * Filters the parameters passed to the TCPDF writeHTMLCell() function.
         *
         * @since 3.3.0
         *
         * @param string $pdf_cell_args See TCPDF function writeHTMLCell() parameters
         * @param array  $cert_args     Array of certificate args.
         * @param array  $tcpdf_params  An array of tcpdf parameters.
         * @param TCPDF  $pdf           `TCPDF` class instance.
         */
        $pdf_cell_args = apply_filters( 'learndash_certification_content_write_cell_args', $pdf_cell_args, $cert_args, $tcpdf_params, $pdf );

        // Print post.
        $pdf->writeHTMLCell(
            $pdf_cell_args['w'],
            $pdf_cell_args['h'],
            $pdf_cell_args['x'],
            $pdf_cell_args['y'],
            $pdf_cell_args['content'],
            $pdf_cell_args['border'],
            $pdf_cell_args['ln'],
            $pdf_cell_args['fill'],
            $pdf_cell_args['reseth'],
            $pdf_cell_args['align'],
            $pdf_cell_args['autopadding']
        );

        /**
         * Fires after the certificate content is added to the PDF.
         *
         * @since 3.3.0
         *
         * @param TCPDF  $pdf       `TCPDF` class instance.
         * @param array  $cert_args Array of certificate args.
         */
        do_action( 'learndash_certification_content_write_cell_after', $pdf, $cert_args );

        // Set background.
        $pdf->SetFillColor( 255, 255, 127 );
        $pdf->setCellPaddings( 0, 0, 0, 0 );
        // Print signature.

        ob_clean();

        // Output pdf document.
        $pdf->Output( $filename . '.pdf', $destination );

        if ( 'F' === $destination ) {
            if ( 'F' === $destination_type ) {
                echo $filename; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            } else {
                echo $cert_args['filename_url']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            }
        }
    }

}