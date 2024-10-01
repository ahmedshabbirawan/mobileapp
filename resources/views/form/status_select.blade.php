<select class="col-xs-10 col-sm-5 form-control" name="status" id="status">
	<option value="publish" <?=($selectedVal == 'publish')?'selected="selected"':''?> >Active</option>
	<option value="draft" <?=($selectedVal == 'draft')?'selected="selected"':''?> >InActive</option>
</select>