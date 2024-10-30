<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_03Acl_00WordPress_Ui_Admin_Settings
{
	protected $wpRoles = array();
	protected $roleOptions = array(
		'admin'		=> '__Administrator__',
		// 'customer'	=> '__Customer__',
		);
	protected $pNames = array();
	protected $readonlyPnames = array();

	public function __construct(
		HC4_Settings_Interface $settings,

		JB7_03Acl_00WordPress_Data_Repo $repoAcl,

		HC4_Html_Input_Select $inputSelect,
		HC4_Html_Input_CheckboxSet $inputCheckboxSet,
		HC4_Html_Input_Checkbox $inputCheckbox,
		HC4_Html_Input_RadioSet $inputRadioSet,

		HC4_Html_Screen_Interface $screen
		)
	{
		global $wp_roles;
		if( ! isset($wp_roles) ){
			$wp_roles = new WP_Roles();
		}

		$this->wpRoles = $repoAcl->getAllRoles();

		$defaultAdminWpRoles = $repoAcl->getDefaultAdminRoles();
		$this->pNames = array();
		foreach( array_keys($this->wpRoles) as $wpRoleName ){
			foreach( array_keys($this->roleOptions) as $roleName ){
				$readonly = ( ('admin' == $roleName) && in_array($wpRoleName, $defaultAdminWpRoles) ) ? TRUE : FALSE;
				if( $readonly ){
					continue;
				}
				$pName = 'users_wp_' . $wpRoleName . '_' . $roleName;
				$this->pNames[] = $pName;
			}
		}
	}

	public function post( $slug, array $post )
	{
		$defaultAdminWpRoles = $this->repoAcl->getDefaultAdminRoles();

		foreach( $this->pNames as $pname ){
			$v = isset($post[$pname]) ? 1 : 0;
			$this->settings->set( $pname, $v );
		}
		$return = array( '-referrer-', '__Settings Updated__' );
		return $return;
	}

	public function get( $slug )
	{
		$values = array();
		foreach( $this->pNames as $pname ){
			$values[$pname] = $this->settings->get( $pname );
		}

		$return = $this->render( $values );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( array $values )
	{
		$defaultAdminWpRoles = $this->repoAcl->getDefaultAdminRoles();

		$wpCountUsers = count_users();
		$wpCountUsers = isset( $wpCountUsers['avail_roles'] ) ? $wpCountUsers['avail_roles'] : array();

		ob_start();
?>

<form method="post" action="HREFPOST:{CURRENT}">

	<div class="hc4-admin-list-primary">
		<div class="hc-grid">
			<div class="hc-col hc-col-4">__WordPress Role__</div>
			<div class="hc-col hc-col-8">__Plugin Role__</div>
		</div>

		<?php foreach( $this->wpRoles as $wpRoleName => $wpRoleLabel ) : ?>
			<?php
			$thisCount = isset( $wpCountUsers[$wpRoleName] ) ? $wpCountUsers[$wpRoleName]  : 0;
			$class = ( $thisCount ) ? 'hc-bold' : '';
			?>
			<div>
				<div class="hc-grid">
					<div class="hc-col hc-col-4 <?php echo $class; ?>">
						<?php echo $wpRoleLabel; ?> (<?php echo $thisCount; ?>)
					</div>
					<div class="hc-col hc-col-8">
						<?php foreach( $this->roleOptions as $roleName => $roleLabel ) : ?>
							<div class="hc-inline-block hc-mx1">
								<?php
								$pName = 'users_wp_' . $wpRoleName . '_' . $roleName;
								$readonly = FALSE;
								if( array_key_exists($pName, $values) ){
									$on = isset($values[$pName]) && $values[$pName];
								}
								else {
									$readonly = TRUE;
								}
								?>
								<?php if( $readonly ) : ?>
									&check; <?php echo $roleLabel; ?>
								<?php else : ?>
									<?php echo $this->inputCheckbox->render( $pName, 1, $on, $roleLabel ); ?>
								<?php endif; ?>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

	<div class="hc4-form-buttons">
		<input type="submit" class="hc4-admin-btn-primary" value="__Save__">
	</div>

</form>

<?php 
		return ob_get_clean();
	}
}