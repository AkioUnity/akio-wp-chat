<?php include 'header.php'; ?>

	<!-- Title -->
	<?php if( !empty( $title ) ): ?>
		<tr>
			<td style="margin: <?php echo $offset . 'px'; ?> 0; padding: <?php echo $offset . 'px'; ?>; padding-bottom: 0;">
				
				<div class="title"><?php echo $title; ?></div>

			</td>
		</tr>
	<?php endif; ?>

	<!-- Content -->
	<?php if( !empty( $content ) ): ?>
		<tr>
			<td style="padding: <?php echo $offset/2 . 'px'; ?> <?php echo $offset . 'px'; ?>; padding-bottom: 0;">
				<div class="content"><?php echo $content; ?></div>
			</td>
		</tr>
	<?php endif; ?>

	<!-- Signature -->
	<?php if( !empty( $signature ) ): ?>
		<tr>
			<td style="padding: <?php echo $offset/2 . 'px'; ?> <?php echo $offset . 'px'; ?>; padding-top: 0;">
				<div class="signature"><?php echo $signature; ?></div>
			</td>
		</tr>
	<?php endif; ?>

<?php include 'footer.php'; ?>