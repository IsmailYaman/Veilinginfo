         <div class="row ">
                  <div class="col-md-4 col-md-offset-4 col-sm-6 col-sm-offset-3 col-xs-10 col-xs-offset-1">

					<?php if(isset($warning)){ ?>  
						  <div class="alert alert-danger alert-dismissable" role="alert"><button type="button" class="close" aria-hidden="true">&times;</button><i class="fa fa-exclamation-circle" aria-hidden="true"></i> <strong>Error!</strong> <?php echo $warning; ?></div>	  
					<?php } ?>

					<?php if(isset($errors) && count($errors) > 0){  ?>
					<div class="alert alert-warning" role="alert">
						<div>
							<i class="fa fa-exclamation-circle" aria-hidden="true"></i>
						</div>
						<ul>
						<?php foreach($errors as $error){  ?>
						<li><?php echo $error; ?></li>
						<?php } ?>
						</ul>
					</div>
					<?php } ?>

                        <div class="panel panel-default">
                            <div class="panel-heading">
                        <strong><?php echo $string_new_password; ?></strong>  
                            </div>
                            <div class="panel-body">
                                <form role="form" action="" method="post">
                                       <br />
                                     <div class="form-group input-group">
                                            <span class="input-group-addon"><i class="fa fa-key"  ></i></span>
                                            <input type="password" class="form-control" placeholder="<?php echo $input_new_password_text; ?>" name="password" autofocus required/>
                                        </div>
										
                                     <div class="form-group input-group">
                                            <span class="input-group-addon"><i class="fa fa-key"  ></i></span>
                                            <input type="password" class="form-control" placeholder="<?php echo $input_new_password_confirm_text; ?>" name="password2" autofocus required/>
                                        </div>
                                     
                                     <button class="btn btn-primary "><?php echo $btn_change; ?></button>
                                    </form>
                            </div>
                           
                        </div>
                    </div>
        </div>