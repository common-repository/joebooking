<?php if (! defined('ABSPATH')) exit; // Exit if accessed directly
class JB7_98Demo_Ui_Massage
{
	public function __construct(
		JB7_11Calendars_Data_Crud	$crudCalendars,
		JB7_12Customers_Data_Crud	$crudCustomers,

		JB7_21Availability_Data_Crud	$crudAvailability,
		JB7_31Bookings_Data_Crud $crudBookings,

		JB7_21Availability_Data_Repo $repoAvailability,
		JB7_41Schedule_Data_Repo $repoSchedule,

		HC4_Time_Interface $t
	)
	{}

	public function get()
	{
	/* CALENDARS */
		$calendars = array();

		$id = 1;
		$calendars[] = array( 'id' => $id++, 'title' => 'Just A Start Massage', 'slot_size' => 15 * 60, 'group_id' => 0 );
		$calendars[] = array( 'id' => $id++, 'title' => 'Welcome Massage', 'slot_size' => 20 * 60, 'group_id' => 0 );
		$calendars[] = array( 'id' => $id++, 'title' => 'Swedish Massage', 'slot_size' => 30 * 60, 'group_id' => 0 );
		$calendars[] = array( 'id' => $id++, 'title' => 'Thai Massage', 'slot_size' => 60 * 60, 'group_id' => 0 );
		$calendars[] = array( 'id' => $id++, 'title' => 'Traditional Massage', 'slot_size' => 90 * 60, 'group_id' => 0 );
		$calendars[] = array( 'id' => $id++, 'title' => 'Super Massage', 'slot_size' => 30 * 60, 'group_id' => 0 );
		$calendars[] = array( 'id' => $id++, 'title' => 'Another Massage', 'slot_size' => 60 * 60, 'group_id' => 0 );
		$calendars[] = array( 'id' => $id++, 'title' => 'Extra Massage', 'slot_size' => 90 * 60, 'group_id' => 0 );
		$calendars[] = array( 'id' => $id++, 'title' => 'So So Massage', 'slot_size' => 30 * 60, 'group_id' => 0 );
		$calendars[] = array( 'id' => $id++, 'title' => 'Medium Massage', 'slot_size' => 60 * 60, 'group_id' => 0 );
		$calendars[] = array( 'id' => $id++, 'title' => 'King Treatment', 'slot_size' => 90 * 60, 'group_id' => 0 );
		$calendars[] = array( 'id' => $id++, 'title' => 'King XL Treatment', 'slot_size' => 120 * 60, 'group_id' => 0 );

		foreach( $calendars as $e ){
			$this->crudCalendars->create( $e );
		}

	/* AVAILABILITY */
		$templ = array(
			'from_time'				=> '32400',
			'to_time'				=> '64800',
			'interval'				=> '1800',
			'applied_on'			=> 'daysofweek',
			'applied_on_details'	=> json_encode( array(1,2,3,4,5) ),
			);

		foreach( $calendars as $calendar ){
			$e = $templ;
			$e['calendar_id'] = $calendar['id'];
			$this->crudAvailability->create( $e );
		}

	/* CUSTOMERS */
		$customers = array();
		$names = array(
			'Haworth, Roy', 'Higgins, Jeremy', 'Lynn, Howard', 'Bird, Ronnie', 'Lawson, Tiarna',
			'Carver, Emmie', 'Hogan, Mayson', 'Broadhurst, Katy', 'Rodriguez, Joseff', 'Devlin, Stuart',
			'Mac, Macie', 'Mcgrath, Subhaan', 'Coleman, Yazmin', 'Jaramillo, Ruben', 'Whitaker, Samantha',
			'Lopez, Bobby', 'Odonnell, Gabriel', 'Hawkins, Hendrix', 'Blackwell, Viktor', 'Nixon, Christy',
			'Beil, Dollie', 'Burton, Harper-Rose', 'Adams, Codie', 'Oakley, Asim', 'Carroll, Hamza',
			'Barton, Arsalan', 'Fernandez, Aiden', 'North, Alasdair', 'Farrow, Nikodem', 'Everett, Chloe-Louise',
			'Ruiz, Amara', 'Boyer, Aida', 'Velazquez, Jaydn', 'Galvan, Nicky', 'Needham, Theia',
			'Oconnell, Maximus', 'Mohamed, Keane', 'Callahan, Lowen', 'Mathews, Ajay', 'Webber, Sidrah',
			'Aguirre, Hadley', 'Matthams, Melinda', 'Rose, Skylar', 'Proctor, Kalvin', 'Hendrix, Ayat',
			'Armitage, Mari', 'Fleming, Anya', 'Morris, Torin', 'Arellano, Deanna', 'Chester, Ilayda',
			);
		$id = 1;
		foreach( $names as $title ){
			$customers[] = array( 'id' => $id++, 'title' => $title );
		}
		foreach( $customers as $e ){
			$this->crudCustomers->create( $e );
		}

	/* BOOKINGS */
	// GET DATES
		$startDate = $this->t->setNow()->setStartWeek()->getDateDb();
		$endDate = $this->t->setDateDb( $startDate )->modify('+1 month')->getDateDb();

		$startDateTime = $this->t->setDateDb( $startDate )->getDateTimeDb();
		$endDateTime = $this->t->setDateDb( $endDate )->getDateTimeDb();

		$slots = $this->repoSchedule->findSlots( $startDateTime, $endDateTime );

		$bookings = array();

		shuffle( $slots );
		$count = 300;
		for( $id = 1; $id <= $count; $id++ ){
			if( $slot = array_shift($slots) ){
				$bookings[] = array(
					'id'					=> $id,
					'start_datetime'	=> $slot->startDateTime,
					'end_datetime'		=> $slot->endDateTime,
					'calendar_id'		=> $slot->calendar->id,
					'customer_id'		=> $customers[ array_rand($customers) ]['id'],
					);
			}
		}

		foreach( $bookings as $e ){
			$this->crudBookings->create( $e );
		}

		return;

		$count = 300;
echo count( $slots );
exit;

		_print_r( $slots );
		exit;



		$dates = array();
		$rexDate = $this->t->setDateDb( $startDate )->getDateDb();
		while( $rexDate < $endDate ){
			$dates[] = $rexDate;
			$rexDate = $this->t->modify('+1 day')->getDateDb();
		}
		$times = array( 10*60*60, 18*60*60, 6*60*60, 12*60*60, 14*60*60, 20*60*60, 23*60*60, 13.5*60*60 );

		$bookings = array();
		$count = 300;
		for( $id = 1; $id <= $count; $id++ ){
			$date = $dates[ array_rand($dates) ];
			$startTime = $times[ array_rand($times) ];
			$calendar = $calendars[ array_rand($calendars) ];
			$duration = $calendar['slot_size'];

			$startDateTime = $this->t->setDateDb( $date )->modify('+' . $startTime . ' seconds')->getDateTimeDb();
			$endDateTime = $this->t->setDateTimeDb( $startDateTime )->modify('+' . $duration . ' seconds')->getDateTimeDb();

			$bookings[] = array(
				'id'					=> $id,
				'start_datetime'	=> $startDateTime,
				'end_datetime'		=> $endDateTime,
				'calendar_id'		=> $calendar['id'],
				);
		}

		foreach( $bookings as $e ){
			$this->crudBookings->create( $e );
		}
	}
}