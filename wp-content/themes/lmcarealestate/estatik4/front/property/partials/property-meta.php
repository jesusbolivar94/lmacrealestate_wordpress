<?php

$property = es_get_property( get_the_ID() );
$fields = es_property_get_meta_fields();

if ( ! empty( $fields ) ) : ?><ul class="es-listing__meta"><?php
    $fields[0]['svg'] = '<svg aria-hidden="true" class="e-font-icon-svg e-fas-bed" viewBox="0 0 640 512" xmlns="http://www.w3.org/2000/svg"><path d="M176 256c44.11 0 80-35.89 80-80s-35.89-80-80-80-80 35.89-80 80 35.89 80 80 80zm352-128H304c-8.84 0-16 7.16-16 16v144H64V80c0-8.84-7.16-16-16-16H16C7.16 64 0 71.16 0 80v352c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16v-48h512v48c0 8.84 7.16 16 16 16h32c8.84 0 16-7.16 16-16V240c0-61.86-50.14-112-112-112z"></path></svg>';
    $fields[1]['svg'] = '<svg aria-hidden="true" class="e-font-icon-svg e-fas-bath" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path d="M32,384a95.4,95.4,0,0,0,32,71.09V496a16,16,0,0,0,16,16h32a16,16,0,0,0,16-16V480H384v16a16,16,0,0,0,16,16h32a16,16,0,0,0,16-16V455.09A95.4,95.4,0,0,0,480,384V336H32ZM496,256H80V69.25a21.26,21.26,0,0,1,36.28-15l19.27,19.26c-13.13,29.88-7.61,59.11,8.62,79.73l-.17.17A16,16,0,0,0,144,176l11.31,11.31a16,16,0,0,0,22.63,0L283.31,81.94a16,16,0,0,0,0-22.63L272,48a16,16,0,0,0-22.62,0l-.17.17c-20.62-16.23-49.83-21.75-79.73-8.62L150.22,20.28A69.25,69.25,0,0,0,32,69.25V256H16A16,16,0,0,0,0,272v16a16,16,0,0,0,16,16H496a16,16,0,0,0,16-16V272A16,16,0,0,0,496,256Z"></path></svg>';
    $fields[2]['svg'] = '<svg aria-hidden="true" class="e-font-icon-svg e-fas-expand" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M0 180V56c0-13.3 10.7-24 24-24h124c6.6 0 12 5.4 12 12v40c0 6.6-5.4 12-12 12H64v84c0 6.6-5.4 12-12 12H12c-6.6 0-12-5.4-12-12zM288 44v40c0 6.6 5.4 12 12 12h84v84c0 6.6 5.4 12 12 12h40c6.6 0 12-5.4 12-12V56c0-13.3-10.7-24-24-24H300c-6.6 0-12 5.4-12 12zm148 276h-40c-6.6 0-12 5.4-12 12v84h-84c-6.6 0-12 5.4-12 12v40c0 6.6 5.4 12 12 12h124c13.3 0 24-10.7 24-24V332c0-6.6-5.4-12-12-12zM160 468v-40c0-6.6-5.4-12-12-12H64v-84c0-6.6-5.4-12-12-12H12c-6.6 0-12 5.4-12 12v124c0 13.3 10.7 24 24 24h124c6.6 0 12-5.4 12-12z"></path></svg>';

	foreach ( $fields as $field ) :
		if ( ! empty( $field['enabled'] ) && ! empty( $property->{$field['field']} )  ) : ?>
            <li class="es-listing__meta-<?php echo $field['field']; ?>">
				<?php if ( ! empty( $use_icons ) ) : ?>
					<?php if ( ! empty( $field['svg'] ) ) : ?>
						<?php echo $field['svg']; ?>
					<?php elseif ( ! empty( $field['icon'] ) ) : ?>
                        <img class="es-meta-icon" src="<?php echo $field['icon'] ?>" alt="<?php printf( __( 'Property %s' ), $field['field'] ); ?>"/>
					<?php endif; ?>
				<?php endif; ?>
				<?php es_the_formatted_field( $field['field'] ); ?>
            </li>
		<?php endif;
	endforeach;
	?></ul><?php
endif;
