<?php

require_once __DIR__ . '/../lib/util.php';

?>
<!doctype html>
<html class="no-js" lang="en">
	<head>
		<meta charset="utf-8">
		<style type="text/css">
			<?php // require_once 'unit-quotation.css'; ?>
		</style>
	</head>
	<body>
		<div class="wrap">
			<div class="head section">
				<img src="dasta-logo-dark-huge.png" height="58" width="276" border="0"/>
				<h2>Concerto</h2>
				<h6>on Sarjapur Road</h6>
			</div>
			<div class="intro section">
				<h1>Hello <?php echo $name ?></h1>
				<h1 class="pre-super">You have chosen apartment</h1>
				<h1 class="super">#<?php echo $unit ?></h1>
				<h1>A <?php echo $bhk ?> on the <?php $floor ?> floor</h1>
			</div>
			<div class="threed section page-break">
				<h1>3D Isometric</h1>
				<img class="force-full" src="../../media/floorplans_3D/<?php echo $image_path ?>" height="500" width="500" border="0">
			</div>
			<pagebreak />
			<div class="twod section page-break">
				<h1>2D unit plan</h1>
			</div>
			<pagebreak />
			<div class="pricing section">
				<h1>Pricing for <em>#<?php echo $unit ?></em></h1>
				<h1>Rate per sft <em><?php echo $quoted_rate ?></em></h1>
				<h3 class="line"><span class="line-line"></span><span class="line-dashed"></span><span class="line-white">Super built-up area <em><?php echo $sft ?> sft</em></span></h3>
				<h6>Garden/terrace area <em><?php echo $gardenterrace ?> sft</em></h6>
				<table>
					<thead>
						<tr>
							<th>Basic cost of flat including alloted <?php echo $carpark_type ?> car park</th>
							<th class="text-right"><?php echo $basiccost_carpark ?></th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td>Cost of garden/terrace</td>
							<td class="text-right"><?php echo $gardenterrace_charge ?></td>
						</tr>
						<tr>
							<td>Floor rise charge</td>
							<td class="text-right"><?php echo $floorise_charge ?></td>
						</tr>
						<tr>
							<td>Corner flat premium charge</td>
							<td class="text-right"><?php echo $corner_premium ?></td>
						</tr>
					</tbody>
				</table>
				<h3>total cost of flat <em><?php echo $total_costofapartment ?></em></h3>
				<table>
					<tbody>
						<!-- <tr>
							<td>Maintenance charges</td>
							<td class="text-right">' . rupee(true) . money_format( '%!.0n', $maintenance_charges ) . '</td>
						</tr> -->
						<tr>
							<td>View Premium</td>
							<td class="text-right"><?php echo $view_premium ?></td>
						</tr>
						<tr>
							<td>Statutory deposit</td>
							<td class="text-right"><?php echo $statutory_deposit ?></td>
						</tr>
						<tr>
							<td>Generator and STP</td>
							<td class="text-right"><?php echo $generator_stp ?></td>
						</tr>
						<tr>
							<td>Club membership charges</td>
							<td class="text-right"><?php echo $club_membership ?></td>
						</tr>
						<tr>
							<td>Legal charges</td>
							<td class="text-right"><?php echo $legal_charges ?></td>
						</tr>
					</tbody>
				</table>
				<h5 class="line"><span class="line-line"></span><span>Modifications</span></h5>
				<table>
					<tbody>
						<tr>
							<td>Collapsible 3rd bedroom wall</td>
							<td class="text-right"><?php echo $mod_collapsable_bedroom_wall ?></td>
						</tr>
						<tr>
							<td>Pooja Room</td>
							<td class="text-right"><?php echo $mod_pooja_room ?></td>
						</tr>
						<tr>
							<td>Living & Dining room swap</td>
							<td class="text-right"><?php echo $mod_living_dining_room_swap ?></td>
						</tr>
						<tr>
							<td><?php if ( intval( $floor ) <= 3 ) { echo 'Upgraded to a covered car park'; } else { echo 'Downgraded to a semi-covered car park'; } ?></td>
							<td class="text-right"><?php echo $carpark_premium_bonus ?></td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="grand-total section page-break">
				<h1><em>Grand total <?php echo $total_grand ?></em></h1>
				<p><img src="/warning.png" height="30" width="33" border="0"/>&nbsp;&nbsp; Please note: This price is only valid till <?php echo date('jS M Y', strtotime('+ 20 days')) ?></p>
			</div>
			<pagebreak />
			<div class="schedule section page-break">
				<h1><em>Payment schedule</em></h1>
				<table>
					<tbody>
						<tr>
							<td>Booking amount</td>
							<td class="line">&nbsp;</td>
							<td class="text-right"><?php echo Util\formatToINR( 200000 ) ?></td>
							<td class="text-right"></td>
						</tr>
						<tr>
							<td>Payment on Sale Agreement</td>
							<td class="line">&nbsp;</td>
							<td class="text-right"><?php echo Util\formatToINR( 0.2 * $total_grand - 200000 ) ?></td>
							<td class="text-right">20%</td>
						</tr>
						<tr>
							<td>Payment on Sale Deed</td>
							<td class="line">&nbsp;</td>
							<td class="text-right"><?php echo Util\formatToINR( 0.8 * $total_grand ) ?></td>
							<td class="text-right">80%</td>
						</tr>
					</tbody>
					<tfoot>
						<tr>
							<td>Total</td>
							<td class="line">&nbsp;</td>
<td class="text-right"><?php echo Util\formatToINR( $total_grand ) ?></td>
							<td class="text-right">100%</td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>
	</body>
</html>
