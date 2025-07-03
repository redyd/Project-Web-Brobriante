<ul class="info info-spacer flex-wrap clear-list">
    <?php if (isset($zone) && isset($emplacement)): ?>
    <li class="flex-center">
        <span class="small-txt"><?php echo $zone ?></span>
        <img src="images/icon/maps.svg" alt="maps" class="small-icon">
    </li>
    <li class="flex-center">
        <span class="small-txt">Emplacement <?php echo $emplacement ?></span>
        <img src="images/icon/home.svg" alt="Localisation" class="small-icon">
    </li>
    <?php endif; ?>
    <?php if (isset($full_name) && !empty($full_name)): ?>
        <li class="flex-center">
            <span class="linked"><a href="detailBrocanteur.php?id=<?php echo urlencode($id) ?>" class="small-txt"><?php echo $full_name ?></a></span>
            <img src="images/icon/person.svg" alt="Localisation" class="small-icon">
        </li>
    <?php endif; ?>
</ul>