 <section class="section">
  <div class="section-header">
    <h1><i class="fab fa-facebook-messenger"></i> <?php echo lang('Messenger Subscriber'); ?></h1>
    <div class="section-header-breadcrumb">
      <div class="breadcrumb-item"><?php echo $page_title; ?></div>
    </div>
  </div>

  <div class="section-body">
    <div class="row">

      <div class="col-lg-4">
        <div class="card card-large-icons">
          <div class="card-icon text-primary">
            <i class="fas fa-sync-alt"></i>
          </div>
          <div class="card-body">
            <h4><?php echo lang('Sync Subscribers'); ?></h4>
            <p><?php echo lang('Sync, migrate, conversation...'); ?></p>
            <a href="<?php echo base_url("subscriber_manager/sync_subscribers/0"); ?>" class="card-cta"><i class="fab fa-facebook"></i> <?php echo lang('Facebook'); ?></a>
            <a href="<?php echo base_url("subscriber_manager/sync_subscribers/1"); ?>" class="card-cta"><i class="fab fa-instagram"></i> <?php echo lang('Instagram'); ?></a>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card card-large-icons">
          <div class="card-icon text-primary">
            <i class="fas fa-user-circle"></i>
          </div>
          <div class="card-body">
            <h4><?php echo lang('Bot Subscribers'); ?></h4>
            <p><?php echo lang('Subscriber actions, assign label, download...'); ?></p>
            <a href="<?php echo base_url("subscriber_manager/bot_subscribers"); ?>" class="card-cta"><?php echo lang('Actions'); ?> <i class="fas fa-chevron-right"></i></a>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card card-large-icons">
          <div class="card-icon text-primary">
            <i class="fas fa-tags"></i>
          </div>
          <div class="card-body">
            <h4><?php echo lang('Labels/Tags'); ?></h4>
            <p><?php echo lang('Subcriber label/tags, segmentation...'); ?></p>
            <a href="<?php echo base_url("subscriber_manager/contact_group"); ?>" class="card-cta"><?php echo lang('Actions'); ?> <i class="fas fa-chevron-right"></i></a>
          </div>
        </div>
      </div>

    </div>
  </div>
</section>