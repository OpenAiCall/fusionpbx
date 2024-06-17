<?php

if ($domains_processed == 1) {

	//clear the array if it exists
		if (isset($array)) {
			unset($array);
		}

	//make the default groups exist
		$group = new groups;
		$group->defaults();

	//get the groups
		$sql = "select * from v_groups ";
		$sql .= "where domain_uuid is null ";
		$database = new database;
		$groups = $database->select($sql, null, 'all');

	//get the dashboard
		$sql = "select ";
		$sql .= "dashboard_uuid, ";
		$sql .= "dashboard_name, ";
		$sql .= "dashboard_path, ";
		$sql .= "dashboard_order, ";
		$sql .= "cast(dashboard_enabled as text), ";
		$sql .= "dashboard_description ";
		$sql .= "from v_dashboard ";
		$database = new database;
		$dashboard_widgets = $database->select($sql, null, 'all');
		unset($sql, $parameters);

	//add the dashboard widgets
		$config_files = glob($_SERVER["DOCUMENT_ROOT"].PROJECT_PATH.'/*/*/resources/dashboard/config.php');
		$x = 0;
		foreach($config_files as $file) {
			include ($file);
			$x++;
		}
		$widgets = $array;
		unset($array);

	//build the array
		$x = 0;
		foreach($widgets['dashboard'] as $row) {
			//check if the dashboard widget is already in the database
			$dashboard_found = false;
			foreach($dashboard_widgets as $dashboard_widget) {
				if ($dashboard_widget['dashboard_uuid'] == $row['dashboard_uuid']) {
					$dashboard_found = true;
				}
			}

			//add the dashboard widget to the array
			if (!$dashboard_found) {
				$array['dashboard'][$x]['dashboard_uuid'] = $row['dashboard_uuid'];
				$array['dashboard'][$x]['dashboard_name'] = $row['dashboard_name'];
				$array['dashboard'][$x]['dashboard_path'] = $row['dashboard_path'];
				$array['dashboard'][$x]['dashboard_column_span'] = $row['dashboard_column_span'] ?? 1;
				$array['dashboard'][$x]['dashboard_row_span'] = $row['dashboard_row_span'] ?? 2;
				$array['dashboard'][$x]['dashboard_order'] = $row['dashboard_order'];
				$array['dashboard'][$x]['dashboard_enabled'] = $row['dashboard_enabled'];
				$array['dashboard'][$x]['dashboard_description'] = $row['dashboard_description'];
				if (!empty($row['dashboard_details_state'])) { $array['dashboard'][$x]['dashboard_details_state'] = $row['dashboard_details_state']; }
				if (!empty($row['dashboard_heading_text_color'])) { $array['dashboard'][$x]['dashboard_heading_text_color'] = $row['dashboard_heading_text_color']; }
				if (!empty($row['dashboard_number_text_color'])) { $array['dashboard'][$x]['dashboard_number_text_color'] = $row['dashboard_number_text_color']; }
				if (!empty($row['dashboard_icon'])) { $array['dashboard'][$x]['dashboard_icon'] = $row['dashboard_icon']; }
				if (!empty($row['dashboard_url'])) { $array['dashboard'][$x]['dashboard_url'] = $row['dashboard_url']; }
				if (!empty($row['dashboard_target'])) { $array['dashboard'][$x]['dashboard_target'] = $row['dashboard_target']; }
				if (!empty($row['dashboard_heading_background_color'])) { $array['dashboard'][$x]['dashboard_heading_background_color'] = $row['dashboard_heading_background_color']; }
				if (!empty($row['dashboard_background_color'])) { $array['dashboard'][$x]['dashboard_background_color'] = $row['dashboard_background_color']; }
				if (!empty($row['dashboard_detail_background_color'])) { $array['dashboard'][$x]['dashboard_detail_background_color'] = $row['dashboard_detail_background_color']; }
				if (!empty($row['dashboard_content'])) { $array['dashboard'][$x]['dashboard_content'] = $row['dashboard_content']; }
				if (!empty($row['dashboard_content_details'])) { $array['dashboard'][$x]['dashboard_content_details'] = $row['dashboard_content_details']; }
				$y = 0;
				if (!empty($row['dashboard_groups'])) {
					foreach ($row['dashboard_groups'] as $row) {
						if (isset($row['group_name'])) {
							foreach($groups as $field) {
								if ($row['group_name'] == $field['group_name']) {
									$array['dashboard'][$x]['dashboard_groups'][$y]['dashboard_group_uuid'] = $row['dashboard_group_uuid'];
									$array['dashboard'][$x]['dashboard_groups'][$y]['dashboard_uuid'] = $row['dashboard_uuid'];
									$array['dashboard'][$x]['dashboard_groups'][$y]['group_uuid'] = $field['group_uuid'];
								}
							}
							$y++;
						}
					}
				}
			$x++;
			}
		}

	//add the temporary permissions
		$p = new permissions;
		$p->add('dashboard_add', 'temp');
		$p->add('dashboard_group_add', 'temp');

	//save the data
		if (!empty($array)) {
			$database = new database;
			$database->app_name = 'dashboard';
			$database->app_uuid = '55533bef-4f04-434a-92af-999c1e9927f7';
			$database->save($array, false);
			//$result = $database->message;
			//view_array($result);
		}

	//delete the temporary permissions
		$p->delete('dashboard_add', 'temp');
		$p->delete('dashboard_group_add', 'temp');

}

?>