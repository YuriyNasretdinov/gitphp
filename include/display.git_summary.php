<?php
/*
 *  display.git_summary.php
 *  gitphp: A PHP git repository browser
 *  Component: Display - summary page
 *
 *  Copyright (C) 2008 Christopher Han <xiphux@gmail.com>
 */

 require_once(GITPHP_INCLUDEDIR . 'util.date_str.php');
 require_once(GITPHP_INCLUDEDIR . 'gitutil.git_read_head.php');
 require_once(GITPHP_INCLUDEDIR . 'gitutil.git_read_commit.php');
 require_once(GITPHP_INCLUDEDIR . 'gitutil.git_read_revlist.php');
 require_once(GITPHP_INCLUDEDIR . 'gitutil.git_read_refs.php');
 require_once(GITPHP_INCLUDEDIR . 'gitutil.read_info_ref.php');
 require_once(GITPHP_INCLUDEDIR . 'git/Project.class.php');

function git_summary($projectroot,$project)
{
	global $tpl;

	$cachekey = sha1($project);

	if (!$tpl->is_cached('project.tpl', $cachekey)) {
		$projectObj = new Project($project);

		$descr = $projectObj->GetDescription();
		$head = git_read_head($projectroot . $project);
		$commit = git_read_commit($projectroot . $project, $head);
		$commitdate = date_str($commit['committer_epoch'],$commit['committer_tz']);
		$owner = $projectObj->GetOwner();
		$refs = read_info_ref($projectroot . $project);
		$tpl->assign("head",$head);
		$tpl->assign("description",$descr);
		$tpl->assign("owner",$owner);
		$tpl->assign("lastchange",$commitdate['rfc2822']);
		if (Config::GetInstance()->HasKey('cloneurl'))
			$tpl->assign('cloneurl', Config::GetInstance()->GetValue('cloneurl') . $project);
		if (Config::GetInstance()->HasKey('pushurl'))
			$tpl->assign('pushurl', Config::GetInstance()->GetValue('pushurl') . $project);
		$revlist = git_read_revlist($projectroot . $project, $head, 17);
		foreach ($revlist as $i => $rev) {
			$revdata = array();
			$revco = git_read_commit($projectroot . $project, $rev);
			$authordate = date_str($revco['author_epoch']);
			$revdata["commit"] = $rev;
			if (isset($refs[$rev]))
				$revdata["commitref"] = $refs[$rev];
			$revdata["commitage"] = $revco['age_string'];
			$revdata["commitauthor"] = $revco['author_name'];
			if (strlen($revco['title_short']) < strlen($revco['title'])) {
				$revdata["title"] = $revco['title'];
				$revdata["title_short"] = $revco['title_short'];
			} else
				$revdata["title_short"] = $revco['title'];
			$revlist[$i] = $revdata;
		}
		$tpl->assign("revlist",$revlist);

		$taglist = git_read_refs($projectroot,$project,"refs/tags");
		if (isset($taglist) && (count($taglist) > 0)) {
			foreach ($taglist as $i => $tag) {
				if (isset($tag['comment'])) {
					$com = trim($tag['comment'][0]);
					if (strlen($com) > GITPHP_TRIM_LENGTH)
						$com = substr($trimmed,0,GITPHP_TRIM_LENGTH) . "...";
					$taglist[$i]['comment'] = $com;
				}
			}
			$tpl->assign("taglist",$taglist);
		}

		$headlist = git_read_refs($projectroot,$project,"refs/heads");
		if (isset($headlist) && (count($headlist) > 0)) {
			$tpl->assign("headlist",$headlist);
		}
	}
	$tpl->display('project.tpl', $cachekey);
}

?>
