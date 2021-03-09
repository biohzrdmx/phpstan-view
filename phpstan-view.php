<?php
	/**
	 * PHPStan Viewer
	 * Simple viewer for PHPStan-generated JSON result files
	 * @author biohzrdmx <github.com/biohzrdmx>
	 * @version 1.0
	 * @license MIT
	 */
	$dir = dirname(__FILE__);
	$json_location = 'tests/output/phpstan.json'; # Modify according to your personal liking
	$project = isset( $_GET['project'] ) ? $_GET['project'] : null;
	$json_file = $project ? "{$dir}/{$project}/{$json_location}" : null;
	$output = file_exists($json_file) ? @json_decode( file_get_contents($json_file) ) : null;
	$last_updated = $output ? filemtime($json_file) : null;
	$folders = null;
	if (! $project ) {
		$folders = [];
		$scan = scandir($dir);
		foreach($scan as $file) {
			if ( is_dir("{$dir}/{$file}") ) {
				if ( preg_match('/\.{1,2}/', $file) === 1 ) continue;
				$folders[] = $file;
			}
		}
	}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>PHPStan Viewer</title>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
	<style>
		body {
			display: flex;
			min-height: 100vh;
			flex-direction: column;
		}
		section {
			flex: 1 0 0;
		}
		a {
			text-decoration: none;
		}
		a:hover {
			text-decoration: none;
		}
		h2 {
			margin-bottom: 15px;
		}
		h2 small {
			font-size: 0.65em;
			color: #999999;
		}
		h2 a {
			font-size: 0.65em;
			margin-right: 3px;
			color: #999999;
		}
		h2 a:hover {
			color: #007bff;
		}
		h4 {
			font-weight: 400;
			font-size: 1.35em;
			margin-bottom: 15px;
		}
		.box {
			border: 1px solid #EEEEEE;
			border-radius: 5px;
			padding: 9px 15px;
			margin-bottom: 15px;
		}
		.box dl {
			margin-bottom: 0;
		}
		.error-list {
			margin-bottom: 15px;
		}
		.error-list.list-plain,
		.project-list {
			border: 1px solid #EEEEEE;
			border-radius: 5px;
		}
		.error-list article {
			margin-bottom: 15px;
		}
		.error-list.list-plain article,
		.project-list article {
			padding: 9px 15px;
			margin-bottom: 0;
		}
		.error-list article h5 {
			font-size: 1em;
			font-weight: 400;
			margin-bottom: 0;
			color: #333333;
		}
		.error-list article:hover h5 i {
			color: #dc3545;
		}
		.error-list article h5 i {
			color: #999999;
			margin-right: 3px;
		}
		.error-list article h5:not(:last-child) {
			margin-bottom: 15px;
		}
		.error-list.list-plain article {
			font-size: 0.9em;
		}
		.error-list.list-plain article:not(:last-child),
		.project-list article:not(:last-child) {
			border-bottom: 1px solid #EEEEEE;
		}
		.error-list article .messages {
			border: 1px solid #EEEEEE;
			border-radius: 5px;
		}
		.error-list article .messages .message {
			padding: 7px 15px;
		}
		.error-list article .messages .message:not(:last-child) {
			border-bottom: 1px solid #EEEEEE;
		}
		.error-list article .messages .message:first-child {
			display: block;
		}
		.error-list article .messages .message .message-text {
			font-size: 0.9em;
		}
		.error-list article .messages .message .message-extra {
			color: #777777;
			font-size: 0.8em;
		}
		.project-list article a i {
			color: #999999;
			margin-right: 3px;
		}
		.project-list article:hover a i {
			color: #ffc107;
		}
	</style>
</head>
<body>
	<section>
		<div class="container">
			<div class="p-1">
				<div class="m-3">
					<?php if ($project): ?>
						<h2>
							<a href="<?php echo basename(__FILE__); ?>"><i class="fa fa-fw fa-chevron-left"></i></a>
							<a href="#" class="js-reload"><i class="fa fa-fw fa-sync"></i></a>
							<span>View project</span>
							<small>/ PHPStan Viewer</small>
						</h2>
						<?php if ($output): ?>
							<h4>Project details</h4>
							<div class="box">
								<dl>
									<dt>Name</dt>
									<dd><code><?php echo htmlspecialchars($project); ?></code></dd>
									<dt>Totals</dt>
									<dd><code><?php echo number_format($output->totals->errors); ?></code><span class="text-muted"> / </span><code><?php echo number_format($output->totals->file_errors); ?></code></dd>
									<dt>Last updated</dt>
									<dd><code><?php echo date('Y-m-d H:i:s', $last_updated); ?></code></dd>
								</dl>
							</div>
							<h4>General errors</h4>
							<div class="error-list list-plain errors-general">
								<?php
									if ($output->errors):
										foreach ($output->errors as $error):
								?>
									<article>
										<code><?php echo htmlspecialchars($error); ?></code>
									</article>
								<?php
										endforeach;
									endif;
								?>
							</div>
							<h4>File errors</h4>
							<div class="error-list errors-file">
								<?php
									if ($output->files):
										foreach ($output->files as $file => $data):
								?>
									<article>
										<h5><i class="fas fa-bug"></i> <?php echo $file; ?></h5>
										<?php if ($data->messages): ?>
											<div class="messages">
												<?php foreach ($data->messages as $message): ?>
													<div class="message">
														<div class="message-text"><code><?php echo htmlspecialchars($message->message); ?></code></div>
														<div class="message-extra">Line <?php echo number_format($message->line); ?></div>
													</div>
												<?php endforeach ?>
											</div>
										<?php endif; ?>
									</article>
								<?php
										endforeach;
									endif;
								?>
							</div>
						<?php else: ?>
							<div class="alert alert-danger">Error parsing output file <strong><?php echo htmlspecialchars($json_file); ?></strong> or not found</div>
						<?php endif; ?>
					<?php else: ?>
						<h2>
							<a href="#" class="js-reload"><i class="fa fa-fw fa-sync"></i></a>
							<span>Select project</span>
							<small>/ PHPStan Viewer</small>
						</h2>
						<div class="project-list">
							<?php
								if ($folders):
									foreach ($folders as $folder):
							?>
								<article>
									<a href="?project=<?php echo urlencode($folder); ?>"><i class="far fa-folder"></i> <?php echo htmlspecialchars($folder); ?></a>
								</article>
							<?php
									endforeach;
								endif;
							?>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</section>
	<footer>
		<div class="container">
			<div class="p-1">
				<div class="m-3">
					<p class="text-center">
						<small class="text-muted">Made by <a href="https://github.com/biohzrdmx" target="_blank">biohzrdmx</a> / View on <a href="https://github.com/biohzrdmx" target="_blank">GitHub</a></small>
					</p>
				</div>
			</div>
		</div>
	</footer>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('.js-reload').on('click', function(e) {
				e.preventDefault();
				location.reload();
			});
			$('.js-expand').on('click', function(e) {
				var el = $(this),
					article = el.closest('article');
				e.preventDefault();
				if ( article.hasClass('is-open') ) {
					article.removeClass('is-open');
					el.text('Show all');
				} else {
					article.addClass('is-open');
					el.text('Close');
				}
			});
		});
	</script>
</body>
</html>