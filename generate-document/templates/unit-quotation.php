<?php

require_once __DIR__ . '/../lib/util.php';

use function Util\formatToINR;

$ordinals = [ 'Ground', '1st', '2nd', '3rd', '4th', '5th', '6th', '7th', '8th', '9th', '10th', '11th', '12th', '13th', '14th' ];
if ( $bhk == 1 ) $apartment_type = 'Studio';
else if ( $bhk == 2 ) $apartment_type = '2BHK';
else if ( $bhk == 3 ) $apartment_type = '3BHK';

$floorplan2D = __DIR__ . '/../../media/floorplans_2D/full/' . $image_path;
list( $floorplan2D_W, $floorplan2D_H ) = getimagesize( $floorplan2D );
$floorplan3D = __DIR__ . '/../../media/floorplans_3D/full/' . $image_path;
list( $floorplan3D_W, $floorplan3D_H ) = getimagesize( $floorplan3D );
$keyplan = __DIR__ . '/../../media/keyplans/keyplan-0' . substr( $unit . '', -1 ) . '.png';

?>
<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
	</head>
	<body>
		<div class="wrap">
			<div class="head section">
				<img src="<?php echo __DIR__ ?>/../../media/logos/dasta-logo-dark-huge.png" height="58" width="276" border="0">
				<h2>Concerto</h2>
				<h6>on Sarjapur Road</h6>
			</div>
			<div class="intro section">
				<h1>Hello <?php echo $name ?></h1>
				<h1 class="pre-super">You have chosen apartment</h1>
				<h1 class="super">#<?php echo $unit ?></h1>
				<h1>A <?php echo $apartment_type ?> on the <?php echo $ordinals[ $floor ] ?> floor</h1>
				<h3>Super built-up area <em><?php echo $sft ?> sft</em></h3>
				<h6>Garden/terrace area <em><?php echo $gardenterrace ?> sft</em></h6>
				<h1 class="grand-total"><em>Grand total <?php echo formatToINR( $total_grand ) ?></em></h1>
				<p>( valid till <?php echo date( 'jS M Y', strtotime( '+ 20 days' ) ) ?> )</p>
			</div>
			<div class="threed section page-break">
				<img class="force-full" src="<?php echo $floorplan3D ?>" width="<?php echo $floorplan3D_W ?>" height="<?php echo $floorplan3D_H ?>" border="0">
			</div>
			<pagebreak />
			<div class="twod section page-break">
				<h1>2D unit plan</h1>
				<img src="<?php echo $floorplan2D ?>" width="<?php echo $floorplan2D_W ?>" height="<?php echo $floorplan2D_H ?>" border="0">
				<img src="<?php echo $keyplan ?>" width="330" height="170" border="0">
			</div>
			<pagebreak />
			<div class="pricing section">
				<h1>Pricing for <em>#<?php echo $unit ?></em></h1>
				<h1>Rate per sft <em><?php echo formatToINR( $quoted_rate ) ?></em></h1>
				<h3 class="line"><span class="line-line"></span><span class="line-dashed"></span><span class="line-white">Super built-up area <em><?php echo $sft ?> sft</em></span></h3>
				<h6>Garden/terrace area <em><?php echo $gardenterrace ?> sft</em></h6>
				<table>
					<thead>
						<tr>
							<th>Basic cost of flat including alloted <?php echo $carpark_type == 'c' ? 'covered' : 'semi-covered' ?> car park</th>
							<th class="text-right"><?php echo formatToINR( $basiccost_carpark ) ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Cost of garden/terrace</td>
							<td class="text-right"><?php echo formatToINR( $gardenterrace_charge ) ?></td>
						</tr>
						<tr>
							<td>Floor rise charge</td>
							<td class="text-right"><?php echo formatToINR( $floorise_charge ) ?></td>
						</tr>
						<tr>
							<td>Corner flat premium charge</td>
							<td class="text-right"><?php echo formatToINR( $cornerflat_charge ) ?></td>
						</tr>
						<tr>
							<td>View Premium</td>
							<td class="text-right"><?php echo formatToINR( $view_premium ) ?></td>
						</tr>
					</tbody>
				</table>
				<h3>total cost of flat <em><?php echo formatToINR( $total_costofapartment ) ?></em></h3>
				<table>
					<tbody>
						<tr>
							<td>Statutory deposit</td>
							<td class="text-right"><?php echo formatToINR( $statutory_deposit ) ?></td>
						</tr>
						<tr>
							<td>Generator and STP</td>
							<td class="text-right"><?php echo formatToINR( $generator_stp ) ?></td>
						</tr>
						<tr>
							<td>Club membership charges</td>
							<td class="text-right"><?php echo formatToINR( $club_membership ) ?></td>
						</tr>
						<tr>
							<td>Legal charges</td>
							<td class="text-right"><?php echo formatToINR( $legal_charges ) ?></td>
						</tr>
					</tbody>
				</table>
				<h5 class="line"><span class="line-line"></span><span>Modifications</span></h5>
				<table>
					<tbody>
						<tr>
							<td>Collapsible 3rd bedroom wall</td>
							<td class="text-right"><?php echo formatToINR( $mod_collapsable_bedroom_wall ) ?></td>
						</tr>
						<tr>
							<td>Pooja Room</td>
							<td class="text-right"><?php echo formatToINR( $mod_pooja_room ) ?></td>
						</tr>
						<tr>
							<td>Living & Dining room swap</td>
							<td class="text-right"><?php echo formatToINR( $mod_living_dining_room_swap ) ?></td>
						</tr>
						<tr>
							<td><?php if ( $carpark_type == 'sc' ) { echo 'Upgraded to a covered car park'; } else { echo 'Downgraded to a semi-covered car park'; } ?></td>
							<td class="text-right"><?php echo formatToINR( $carpark_premium_bonus ) ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="grand-total section page-break">
				<h1><em>Grand total <?php echo formatToINR( $total_grand ) ?></em></h1>
				<p><img src="<?php echo __DIR__ ?>/../../media/icons/warning.png" height="30" width="33" border="0"/>&nbsp;&nbsp; Please note: This price is only valid till <?php echo date( 'jS M Y', strtotime( '+ 20 days' ) ) ?></p>
			</div>
			<pagebreak />
			<div class="schedule section page-break">
				<h1><em>Payment schedule</em></h1>
				<table>
					<tbody>
						<tr>
							<td>Booking amount</td>
							<td class="line">&nbsp;</td>
							<td class="text-right"><?php echo formatToINR( 200000 ) ?></td>
							<td class="text-right"></td>
						</tr>
						<tr>
							<td>Payment on Sale Agreement</td>
							<td class="line">&nbsp;</td>
							<td class="text-right"><?php echo formatToINR( 0.2 * $total_grand - 200000 ) ?></td>
							<td class="text-right">20%</td>
						</tr>
						<tr>
							<td>Payment on Sale Deed</td>
							<td class="line">&nbsp;</td>
							<td class="text-right"><?php echo formatToINR( 0.8 * $total_grand ) ?></td>
							<td class="text-right">80%</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td>Total</td>
							<td class="line">&nbsp;</td>
<td class="text-right"><?php echo formatToINR( $total_grand ) ?></td>
							<td class="text-right">100%</td>
						</tr>
					</tfoot>
				</table>
				<ol class="fine-print">
					<li>Cheques to be made in favour of M/s. Urban Heights Sarjapur.</li>
					<li>Stamp duty charges at 0.1% of the total sale consideration should be paid by the client at the time of executing the sale agreement.</li>
					<li>Sale agreement within 15 days from the date of the booking form.</li>
					<li>Sale deed payment within 30 days from the date of the Agreement of Sale.</li>
					<li>Maintenance charges shall be applicable from the date of the Sale Deed payable directly by the client to the Association (DCAOA).</li>
				</ol>
			</div>
		</div>
	</body>
</html>
