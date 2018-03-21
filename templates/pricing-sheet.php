<?php

// require_once '../lib/util.php';

?>

<h1>Pricing Sheet for #<?php echo $unit ?></h1>

<p>Name: <?php echo $name ?></p>
<p>Email: <?php echo $email ?></p>
<p>Phone Number: <?php echo $phoneNumber ?></p>

<h2>Specifications:</h2>
<p>Type: <?php echo $bhk ?></p>
<p>Floor: <?php echo $floor ?></p>
<p>Super Built-up Area: <?php echo $sft ?></p>
<p>Garden / Terrace Area: <?php echo $gardenterrace ?></p>
<p>Rate per sqft: <?php echo $discounted_rate ?></p>
<p>Corner Flat: <?php echo $corner_flat ?></p>
<p>Car Park: <?php echo $carpark_type == 'c' ? 'Covered' : 'Semi-covered' ?></p>

<p>Mod A: <?php echo $mod_collapsable_bedroom_wall ?></p>
<p>Mod B: <?php echo $mod_living_dining_room_swap ?></p>
<p>Mod C: <?php echo $mod_pooja_room ?></p>
<p>Mod D: <?php echo $mod_store_room ?></p>


<h2>Cost breakdown:</h2>
<p>Basic Cost: Rs. <?php echo $basiccost ?></p>
<p>Basic Cost including car park: Rs. <?php echo $basiccost_carpark ?></p>
<p>Garden / Terrace Charge: Rs. <?php echo $gardenterrace_charge ?></p>
<p>Car Park: <?php echo $carkpark ?></p>
<p>Car Park Upgrade/Downgrade: Rs. <?php echo $carpark_premium_bonus ?></p>
<p>Corner Flat Charge: Rs. <?php echo $cornerflat_charge ?></p>
<p>Floor Rise Charge: Rs. <?php echo $floorise_charge ?></p>
<p>Total Cost: Rs. <?php echo $total_costofapartment ?></p>

<h3>Modifications:</h3>
<p>Collapsible Bedroom Wall: <?php echo $collapsibleBedroomWall ?></p>
<p>Living / Dining Swap: <?php echo $livingDiningSwap ?></p>
<p>Pooja Room: <?php echo $poojaRoom ?></p>
<p>Store Room: <?php echo $storeRoom ?></p>



<p>Statutory Deposit: Rs. <?php echo $statutory_deposit ?></p>

<p>Club Membership: Rs. <?php echo $club_membership ?></p>
<p>Maintenance: Rs. <?php echo $maintenance_charges ?></p>
<p>Generator / STP: Rs. <?php echo $generator_stp ?></p>
<p>Legal: Rs. <?php echo $legal_charges ?></p>

<p>Gross Total: Rs. <?php echo $total_gross ?></p>
<p>GST: Rs. <?php echo $gst ?></p>
<p>Grand Total: Rs. <?php echo $total_grand ?></p>


<!-- <p><?php echo $mod_toggle_collapsable_bedroom_wall ?></p>
<p><?php echo $mod_toggle_living_dining_room_swap ?></p>
<p><?php echo $mod_toggle_pooja_room ?></p>
<p><?php echo $mod_toggle_store_room ?></p>
<p><?php echo $mod_toggle_car_park ?></p> -->
