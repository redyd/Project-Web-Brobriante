<article class="card">
    <div class="fig">
        <img src="<?= $src_item ?>" alt="<?= $name_item ?>" class="img-card">
        <p class="basic-txt compact">ID <?= $id_item ?></p>
        <p class="basic-txt compact"><?= $name_item ?></p>
        <?php if (isset($location_item) && !empty($location_item)): ?>
            <h3 class="small-txt">Emplacement <?= $location_item ?></h3>
        </div>
        <a href="<?= $link_item ?>" class="full-btn blue-btn small-txt">Détail</a>
        <?php else: ?>
            <h3 class="small-txt">Emplacement non-attribué</h3>
        </div>
        <div class="card-btn">
            <a href="attribuerEmplacement.php?id=<?= $id_item ?>" class="grey-btn full-btn">Attribuer</a>
            <a href="<?= $link_item ?>" class="full-btn blue-btn small-txt">Détail</a>
        </div>
    <?php endif; ?>
</article>