<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_43Manage_Ui_Bookings_Id_Audit
{
	public function __construct(
		HC4_Time_Format $tf,

		JB7_01Users_Data_Repo $repoUsers,
		JB7_01Users_Ui_Title $viewUser,

		JB7_11Calendars_Data_Repo $repoCalendars,
		JB7_11Calendars_Ui_Title $viewCalendar,

		JB7_31Bookings_Data_Repo $repoBookings,
		JB7_31Bookings_Ui_Status $viewStatus,
		JB7_31Bookings_Ui_PaymentStatus $viewPaymentStatus,

		JB7_04Audit_Data_Repo $repoAudit,

		HC4_Html_Screen_Interface $screen
	)
	{}

	public function get( $slug, $id )
	{
		$model = $this->repoBookings->findById( $id );
		$entries = $this->repoAudit->read( 'bookings', $id );

		$calendars = $this->repoCalendars->findAll();

		$return = $this->render( $model, $entries, $calendars );
		$return = $this->screen->render( $slug, $return );
		return $return;
	}

	public function render( JB7_31Bookings_Data_Model $model, array $entries, array $calendars )
	{
		if( ! $entries ){
			return;
		}

	// GROUP ENTRIES BY EVENT TIME AND USER
		$groupedEntries = array();
		foreach( $entries as $e ){
			$groupKey = $e['event_datetime'] . '-' . $e['user_id'];
			if( ! isset($groupedEntries[$groupKey]) ){
				$groupedEntries[$groupKey] = array();
			}
			$groupedEntries[$groupKey][] = $e;
		}

		ob_start();
?>

<div class="hc4-admin-list-primary">
	<div class="hc-grid hc-fs2 hc-muted2 hc-xs-hide">
		<div class="hc-col hc-col-3">__Date and Time__</div>
		<div class="hc-col hc-col-6">__Details__</div>
		<div class="hc-col hc-col-3">__User__</div>
	</div>

	<?php foreach( $groupedEntries as $groupKey => $entries ): ?>
	<div>
		<div class="hc-grid">

			<div class="hc-col hc-col-3">
				<?php
				list( $eventDateTime, $eventUserId ) = explode( '-', $groupKey );
				$eventUserId = $eventUserId ? $eventUserId : 0;
				$user = $this->repoUsers->findById( $eventUserId );
				?>
				<?php echo $this->tf->formatDateWithWeekday( $eventDateTime ); ?> <?php echo $this->tf->formatTime( $eventDateTime ); ?>
			</div>

			<div class="hc-col hc-col-6">
				<div class="hc4-admin-list-secondary">
					<?php foreach( $entries as $e ) : ?>
						<?php
						$view = NULL;
						switch( $e['column_name'] ){
							case 'status':
								$view = $this->viewStatus->render( $e['old_value'] ) . ' &rarr; ' . $this->viewStatus->render( $e['new_value'] );
								break;

							case 'payment_status':
								$view = $this->viewPaymentStatus->render( $e['old_value'] ) . ' &rarr; ' . $this->viewPaymentStatus->render( $e['new_value'] );
								break;

							case 'id':
								$view = '__Created__';
								break;

							case 'end_datetime':
								$oldTimeView = $this->tf->formatTime( $e['old_value'] );
								$newTimeView = $this->tf->formatTime( $e['new_value'] );
								if( $oldTimeView != $newTimeView ){
									$view = $oldTimeView . ' &rarr; ' . $newTimeView;
									$view = '<label class="hc-block hc-fs2 hc-muted2">__End Time__</label>' . $view; 
								}
								break;

							case 'start_datetime':
								$oldDateView = $this->tf->formatDate( $e['old_value'] );
								$newDateView = $this->tf->formatDate( $e['new_value'] );
								$oldTimeView = $this->tf->formatTime( $e['old_value'] );
								$newTimeView = $this->tf->formatTime( $e['new_value'] );

								if( $oldDateView != $newDateView ){
									$view = $oldDateView . ' &rarr; ' . $newDateView;
									$view = '<label class="hc-block hc-fs2 hc-muted2">__Date__</label>' . $view; 
								}
								elseif( $oldTimeView != $newTimeView ){
									$view = $oldTimeView . ' &rarr; ' . $newTimeView;
									$view = '<label class="hc-block hc-fs2 hc-muted2">__Start Time__</label>' . $view; 
								}
								break;

							case 'calendar':
								if( ! $e['old_value'] ){
									if( isset($calendars[$e['new_value']]) ){
										$view = $this->viewDomainTerm->render( $domainTerms[$e['new_value']] );
									}
								}
								else {
									if( isset($calendars[$e['old_value']]) ){
										$view = '<span class="hc-line-through">' . $this->viewDomainTerm->render( $domainTerms[$e['old_value']] ) . '</span>';
									}
								}
								break;
						}
						?>
						<?php
						if( ! $view ){
							continue;
						}
						?>

						<div>
							<?php echo $view; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="hc-col hc-col-3">
				<?php if( $user->id ) : ?>
					<?php echo $this->viewUser->render( $user ); ?>
				<?php else : ?>
					- __Online Form__ -
				<?php endif; ?>
			</div>

		</div>
	</div>
	<?php endforeach; ?>
</div>

<?php 
		return ob_get_clean();
	}
}