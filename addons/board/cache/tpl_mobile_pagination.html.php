<?php if (!defined('IN_PHPBB')) exit; if ($this->_rootref['PAGINATION']) {  ?>

 <b>
  <?php if ($this->_rootref['PREVIOUS_PAGE']) {  ?>

   <a href="<?php echo (isset($this->_rootref['PREVIOUS_PAGE'])) ? $this->_rootref['PREVIOUS_PAGE'] : ''; ?>"><?php echo ((isset($this->_rootref['L_PREVIOUS'])) ? $this->_rootref['L_PREVIOUS'] : ((isset($user->lang['PREVIOUS'])) ? $user->lang['PREVIOUS'] : '{ PREVIOUS }')); ?></a>&nbsp;&nbsp;
  <?php } ?>

  <?php echo (isset($this->_rootref['PAGINATION'])) ? $this->_rootref['PAGINATION'] : ''; ?>

  <?php if ($this->_rootref['NEXT_PAGE']) {  ?>

   &nbsp;<a href="<?php echo (isset($this->_rootref['NEXT_PAGE'])) ? $this->_rootref['NEXT_PAGE'] : ''; ?>"><?php echo ((isset($this->_rootref['L_NEXT'])) ? $this->_rootref['L_NEXT'] : ((isset($user->lang['NEXT'])) ? $user->lang['NEXT'] : '{ NEXT }')); ?></a>
  <?php } ?>

 </b>
<?php } ?>