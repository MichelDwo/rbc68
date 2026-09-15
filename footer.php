<?php
/**
 * Pied de page du thème.
 *
 * @package RBC68
 */
?>
<footer class="site-footer">
	<div class="footer-content">
		<div class="footer-info">
			<div class="footer-address"><?php echo esc_html( rbc68_mod( 'rbc68_address', 'Complexe sportif C.M.C.A.S, chemin de Brunstatt, 68170 Rixheim' ) ); ?></div>
			<p>Riedisheim Badminton Club — Créé en 2016</p>
		</div>
		<div class="footer-copyright">&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> RBC68. Tous droits réservés.</div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>

