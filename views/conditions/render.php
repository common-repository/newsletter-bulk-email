<div class="bulkmail-conditions-render" data-emptytext="<?php esc_attr_e( 'No Conditions defined', 'bulkmail' ); ?>">
	<?php foreach ( $conditions as $i => $condition_group ) : ?>
	<div class="bulkmail-condition-render-group">
		<?php
		if ( $i ) {
			echo '<span class="bulkmail-condition-operators">' . esc_html__( 'and', 'bulkmail' ) . '</span>';
		}
		foreach ( $condition_group as $j => $condition ) :
			$field    = isset( $condition['field'] ) ? $condition['field'] : $condition[0];
			$operator = isset( $condition['operator'] ) ? $condition['operator'] : $condition[1];
			$value    = isset( $condition['value'] ) ? $condition['value'] : $condition[2];
			$nice     = $this->print_condition( $condition );
			?>
		<div class="bulkmail-condition-render bulkmail-condition-render-<?php echo esc_attr( $condition['field'] ); ?>" title="<?php echo esc_attr( strip_tags( sprintf( '%s %s %s', $nice['field'], $nice['operator'], $nice['value'] ) ) ); ?>">
			<?php
			if ( $j ) {
				echo '<span class="bulkmail-condition-type bulkmail-condition-operators">' . esc_html__( 'or', 'bulkmail' ) . '</span>';
			}
			?>
		<span class="bulkmail-condition-type bulkmail-condition-field"><?php echo $nice['field']; ?></span>
		<span class="bulkmail-condition-type bulkmail-condition-operator"><?php echo $nice['operator']; ?></span>
		<span class="bulkmail-condition-type bulkmail-condition-value"><?php echo $nice['value']; ?></span>
		</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>
</div>
