<?php
print_r($_POST);
if($action == 'add' || $action == 'edit') {

	$form = form::factory('slots', 'id='.$id, 'index.php');
	
	$field = $form->addTextField('name', $form->get('name'));
	$field->fieldName(lang::get('name'));
	
	$field = $form->addTextField('description', $form->get('description'));
	$field->fieldName(lang::get('description'));
	
	$field = $form->addRawField('<select name="modul_id" class="form-control">'.pageAreaHtml::moduleList($form->get('modul_id')).'</select>');
	$field->fieldName(lang::get('modul'));
	
	if($action == 'edit') {
		$form->addHiddenField('id', $id);	
	}
	
	if($form->isSubmit()) {
		
		$form->addPost('modul_id', $form->get('modul_id'));
			
	}
	
?>
	<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><?php echo lang::get('slots_add'); ?></h3>
            </div>
            <div class="panel-body">
            	<?php echo $form->show(); ?>
            </div>
        </div>
    </div>
</div>    
<?php
}

if($action == 'show') {

	$sql = sql::factory();
	$sql->query('SELECT * FROM '.sql::table('slots').' WHERE id='.$id)->result();
	$pageArea = new pageArea($sql);
	
	$form = form::factory('module', 'id='.$sql->get('modul_id'), 'index.php');
	$form->setSave(false);
	$form->addFormAttribute('class', '');
	
	$input = $pageArea->OutputFilter($form->get('input'), $sql);
	$form->addRawField($input);
?>
<div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">"<?php echo $sql->get('name') ?>" <?php echo lang::get('edit'); ?></h3>
                </div>
            	<div class="panel-body">
					<?php echo $form->show(); ?>
                </div>
            </div>
        </div>
    </div>
<?php
	
}

if($action == '') {

	$table = table::factory();
	
	$table->addCollsLayout('200,*,200');
	
	$table->addRow()
	->addCell(lang::get('name'))
	->addCell(lang::get('description'))
	->addCell(lang::get('action'));
	
	$table->addSection('tbody');
	$table->setSql('SELECT * FROM '.sql::table('slots'));
	while($table->isNext()) {
		
		$name = '<a href="'.url::backend('structure', ['subpage'=>'slots', 'action'=>'show', 'id'=>$table->get('id')]).'">'.$table->get('name').'</a>';
		$edit = '<a href='.url::backend('structure', ['subpage'=>'slots', 'action'=>'edit', 'id'=>$table->get('id')]).' class="btn btn-sm btn-default">'.lang::get('edit').'</a>';
		$delete = '<a href='.url::backend('structure', ['subpage'=>'slots', 'action'=>'delete', 'id'=>$table->get('id')]).' class="btn btn-sm btn-danger">'.lang::get('delete').'</a>';
		
		$table->addRow()
		->addCell($name)
		->addCell($table->get('description'))
		->addCell('<span class="btn-group">'.$edit.$delete.'</span>');
		
		$table->next();
	}
?>
<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title pull-left"><?php echo lang::get('slots_current_page'); ?></h3>
                <span class="btn-group pull-right">
                	<a href="<?php echo url::backend('structure', ['subpage'=>'slots', 'action'=>'add']); ?>" class="btn btn-default"><?php echo lang::get('add'); ?></a>
                </span>
                <div class="clearfix"></div>
            </div>
            <?php echo $table->show(); ?>
        </div>
    </div>
</div>
<?php
}
?>