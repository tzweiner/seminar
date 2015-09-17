<?php get_header(); ?>
<?php if ( have_posts() ) : ?>

	<?php while (have_posts()) : the_post(); ?>
	<h2><span><?php the_title(); ?></span></h2>
	<div class="c12 row">
		<?php the_content(); ?>
		
		<?php 
		$class_categories = array (7, 6, 8, 9);		// 'instrument', 'singing', 'dance', 'language'		
		?>
		
		<?php 
		$args = array (
			'post_type'	=> 'classes',
			'post_status'	=> 'publish',
			'posts_per_page'	=> -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			
		);
		
		$my_query = new WP_Query ($args);
		
		
		if ($my_query->have_posts()): ?>
		<h3 class="margin">Instrumental, Vocal & Language Teaching Staff</h3>
		<?php 
			while ($my_query->have_posts()): $my_query->the_post();
			$class_name = get_the_title();
			if ($class_name == 'Dance') continue;
			$args = array (
			'post_type'	=> 'teachers',
			'post_status'	=> 'publish',
			'posts_per_page'	=> -1,
			'orderby' => 'menu_order',
			'order' => 'ASC',
			'meta_query' => array (
				array (
					'key' => 'specialty',
					'value' => $class_name,
					'compare' => '='
				)						
			)
						
			);
			
			$teacher_query = new WP_Query ($args);
			
			if ($teacher_query->have_posts()):  
			
				while ($teacher_query->have_posts()): $teacher_query->the_post(); 
					$person_id = get_the_ID();
					$thumbnail = get_attachment_image_url (get_post_thumbnail_id( $person_id ), 'full');					
					
			?>
				
			<a class="person-wrapper fancybox" rel="teacher-group" href="#person-wrapper-<?php echo $person_id; ?>">
				<div class="hover-overlay"></div>
				<div class="person-photo<?php if (!$thumbnail): ?> no-photo<?php endif; ?>" style="<?php if ($thumbnail): ?> background-image: url('<?php echo $thumbnail; ?>');<?php endif; ?>"></div>
				<div class="person-info<?php if (!$thumbnail): ?> no-photo<?php endif; ?>">
					<div class="person-name"><?php echo get_the_title(); ?></div>
					<div class="person-position"><?php echo getCorrectClassName ($class_name); ?></div>	
				</div>
			</a>
			
			<div class="person-wrapper-content" id="person-wrapper-<?php echo $person_id; ?>">
				<div class="person-photo<?php if (!$thumbnail): ?> no-photo<?php endif; ?>"><?php if ($thumbnail): ?><img src="<?php echo $thumbnail; ?>" /><?php endif; ?></div>
				<div class="person-info<?php if (!$thumbnail): ?> no-photo<?php endif; ?>">
					<div class="person-name"><?php echo get_the_title(); ?></div>
					
					<div class="person-position"><?php echo getCorrectClassName ($class_name); ?></div>	
						
				</div>
				<div class="person-content">
					<?php echo apply_filters ('the_content', $post->post_content); ?>
					<?php 
					$videos = get_field ('videos_repeater');
					if ($videos): ?>
					<div class="videos-wrapper">
					<?php foreach ($videos as $vid): ?>
						<a href="<?php echo $vid ['video_link']; ?>" target="_blank"><?php echo $vid ['video_link_text']; ?></a><br />
					<?php endforeach; ?>
					</div>					
					<?php endif; 
					?>
				</div>
			</div>
				<?php 
				endwhile;
						
			endif;
			
			wp_reset_postdata();
			wp_reset_query();
			
		?>
		
				
		<?php 
			endwhile;
		else: ?>
		<p>No information found.</p>
		
		<?php endif;
		
		wp_reset_postdata();
		wp_reset_query();

		?>
		
		<h3 class="margin">Dance Teachers & Village Dance Groups</h3>
		<?php 
		$args = array (
			'post_type'	=> 'dance-teachers',
			'post_status'	=> 'publish',
			'posts_per_page'	=> -1,
			'meta_key'		=> 'from_date',
			'orderby' => 'meta_value_num',
			'order' => 'ASC'
						
			);
			
			$dance_teacher_query = new WP_Query ($args);
			if ($dance_teacher_query->have_posts()) {
				$count = 0;
				while ($dance_teacher_query->have_posts()) {
					$dance_teacher_query->the_post();
					$person_id = get_the_ID();
					
					$thumbnail = get_attachment_image_url (get_post_thumbnail_id( $person_id ), 'full'); ?>
					
					<a class="person-wrapper dance-group fancybox<?php if ($count % 2 == 0) echo ' odd'; ?>" rel="dance-teacher-group" href="#group-wrapper-<?php echo $person_id; ?>">
						<div class="hover-overlay"></div>
						<div class="person-photo<?php if (!$thumbnail): ?> no-photo<?php endif; ?>" style="<?php if ($thumbnail): ?> background-image: url('<?php echo $thumbnail; ?>');<?php endif; ?>"></div>
						<div class="person-info<?php if (!$thumbnail): ?> no-photo<?php endif; ?>">
							<span class="person-date"><?php echo get_dancegroup_date ($person_id); ?></span> &#8212; 
							<span class="person-position"><?php echo get_field ('region'); ?></span>								
							<div class="person-name"><?php echo trim_text (get_the_title(), 120); ?></div>
						</div>
					</a>
					
					<div class="person-wrapper-content" id="group-wrapper-<?php echo $person_id; ?>">
						<div class="person-photo<?php if (!$thumbnail): ?> no-photo<?php endif; ?>"><?php if ($thumbnail): ?><img src="<?php echo $thumbnail; ?>" /><?php endif; ?></div>
						<div class="person-info<?php if (!$thumbnail): ?> no-photo<?php endif; ?>">
							<div class="person-name"><?php echo get_the_title(); ?></div>
							
							<div class="person-position"><?php echo get_field ('region'); ?></div>	
							<div class="person-date"><?php echo get_dancegroup_date ($person_id); ?></div>
								
						</div>
						<div class="person-content">
							<?php echo apply_filters ('the_content', $post->post_content); ?>
							<?php 
							$video = get_field ('video_url');
							if ($video): ?>
							<div class="videos-wrapper">
							
								<a href="<?php echo $video; ?>" target="_blank">Watch Video</a><br />
							
							</div>					
							<?php endif; 
							?>
						</div>
					</div> <?php 
					$count++;
				}
			}
			else {
				?>
				<p>No dance groups listed at this time.</p> <?php 
			}
			wp_reset_postdata();
			wp_reset_query();
		?>
		
		<h3 class="margin">Program Coordinators</h3>
		
		<?php 
		// find all coordinators and see if there are any duplicates
		$ids = array ();
		$instrument_program_coordinator = get_field ('instrument_program_coordinator');		
		$dance_program_coordinator = get_field ('dance_program_coordinator');
		$vocal_music_program_coordinator = get_field ('vocal_music_program_coordinator');
		$assistant_coordinator = get_field ('dance_program_teacher_assistant');
		
		$positions = array ();
		
		$field_key = 'field_5484b5943ddff';	// instruments
		$object = get_field_object ($field_key, get_the_ID());
		$object_label = $object['label'];
		foreach ($instrument_program_coordinator as $coord) {
			$coord_id = $coord->ID;
			if (!isset ($positions [$coord_id])) {
				$positions [$coord_id] = array ();
			}
			if (!array_key_exists ($coord_id, $positions)) {
				$positions [$coord_id] = array ();
			}
			
			if (!in_array ($object_label, $positions[$coord_id])) {
				$positions [$coord_id] [] = $object_label;
			}
						
		}
		
		$field_key = 'field_5484b5e73de00';	// dance
		$object = get_field_object ($field_key, get_the_ID());
		$object_label = $object['label'];
		foreach ($dance_program_coordinator as $coord) {
			$coord_id = $coord->ID;
			if (!isset ($positions [$coord_id])) {
				$positions [$coord_id] = array ();
			}
			if (!array_key_exists ($coord_id, $positions)) {
				$positions [$coord_id] = array ();
			}
				
			if (!in_array ($object_label, $positions[$coord_id])) {
				$positions [$coord_id] [] = $object_label;
			}
		
		}
		
		$field_key = 'field_5484b6003de01';	// vocal
		$object = get_field_object ($field_key, get_the_ID());
		$object_label = $object['label'];
		foreach ($vocal_music_program_coordinator as $coord) {
			$coord_id = $coord->ID;
			if (!isset ($positions [$coord_id])) {
				$positions [$coord_id] = array ();
			}
			if (!array_key_exists ($coord_id, $positions)) {
				$positions [$coord_id] = array ();
			}
		
			if (!in_array ($object_label, $positions[$coord_id])) {
				$positions [$coord_id] [] = $object_label;
			}
		
		}
		
		$field_key = 'field_5484b61b3de02';	// assistant
		$object = get_field_object ($field_key, get_the_ID());
		$object_label = $object['label'];
		foreach ($assistant_coordinator as $coord) {
			$coord_id = $coord->ID;
			if (!isset ($positions [$coord_id])) {
				$positions [$coord_id] = array ();
			}
			if (!array_key_exists ($coord_id, $positions)) {
				$positions [$coord_id] = array ();
			}
		
			if (!in_array ($object_label, $positions[$coord_id])) {
				$positions [$coord_id] [] = $object_label;
			}
		
		}
				
		foreach ($positions as $person_id=>$positions_list) {
			$person = get_post ($person_id);
			$thumbnail = get_attachment_image_url (get_post_thumbnail_id( $person_id ), 'full');
			
			?>
			<a class="person-wrapper coordinator fancybox" rel="program-group" href="#person-wrapper-coordinator-<?php echo $person_id; ?>">
				<div class="hover-overlay"></div>				
				<div class="person-photo<?php if (!$thumbnail): ?> no-photo<?php endif; ?>" style="<?php if ($thumbnail): ?> background-image: url('<?php echo $thumbnail; ?>');<?php endif; ?>"></div>				
				<div class="person-info<?php if (!$thumbnail): ?> no-photo<?php endif; ?>">
					<div class="person-name"><?php echo $person->post_title; ?></div>
					<?php foreach ($positions_list as $pos): ?>
					<div class="person-position">&raquo; <?php echo $pos; ?></div>	
					<?php endforeach; ?>		
				</div>
			</a>
			
			<div class="person-wrapper-content" id="person-wrapper-coordinator-<?php echo $person_id; ?>">
				<div class="person-photo<?php if (!$thumbnail): ?> no-photo<?php endif; ?>"><?php if ($thumbnail): ?><img src="<?php echo $thumbnail; ?>" /><?php endif; ?></div>
				<div class="person-info<?php if (!$thumbnail): ?> no-photo<?php endif; ?>">
					<div class="person-name"><?php echo $person->post_title; ?></div>
					<?php foreach ($positions [$person_id] as $pos): ?>
					<div class="person-position"><?php echo $pos; ?></div>	
					<?php endforeach; ?>		
				</div>
				<div class="person-content">
					<?php echo apply_filters ('the_content', $person->post_content); ?>
					<?php 
					$videos = get_field ('videos_repeater', $person_id);
					if ($videos): ?>
					<div class="videos-wrapper">
					<?php foreach ($videos as $vid): ?>
						<a href="<?php echo $vid ['video_link']; ?>" target="_blank"><?php echo $vid ['video_link_text']; ?></a><br />
					<?php endforeach; ?>
					</div>					
					<?php endif; 
					?>
				</div>
			</div>
			<?php 
		}
		?>
	</div> 
	
	<h3 class="margin">Teaching Staff Archive</h3>
	
	<div class="previous-years-wrapper">
		<select name="previous_years" class="previous_years">
			<option value="">- Select Year -</option> 
			<?php $previous_years_updated = get_field('previous_years_archive_after_2014_repeater');
			foreach ($previous_years_updated[0] as $year): ?>
			<option value="/teachers-staff?filter=<?php echo $year; ?>"><?php echo $year; ?></option>
			<?php 
			endforeach;
			
			$previous_years = get_field ('previous_years_archive');
	
			foreach ($previous_years as $year):
			?>
			<option value="<?php echo $year ['pdf_of_teaching_staff']; ?>"><?php echo $year ['teaching_staff_archive_year']; ?></option>
			<?php endforeach; ?>
		</select>
		<a href="#" class="fancypdf" name="all" id="previous_years_button">View Staff</a>
	</div>
	
	
	
	
	<?php endwhile;
endif; ?>
<?php get_footer(); ?>
