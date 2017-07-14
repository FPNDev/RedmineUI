<?php
	class tickets {

		public static $error = '';
		public static function get($cookie = false) {
			if(!is_array($cookie)) $cookie = cookie::get();
			if(!$cookie) return false;
			$tickets = Request::send('GET', $cookie['url'].'/issues?assigned_to_id=me&per_page=1000', ['Cookie', 'autologin='.urlencode($cookie['autologin'])]);
			if($tickets) {
				if(strpos($tickets, 'loggedas')) {
					$tickets = str_get_html(str_replace(["\n", '<!DOCTYPE html>'], '', $tickets));
					$tickets = $tickets->find('.issues', 0)->find('.issue');
					$ret = [];
					$categories = ['project' => ['Все проекты'], 'status' => ['Все состояния'], 'type' => ['Все виды'], 'priority' => ['Все приоритеты']];
					foreach($tickets as $ticket) {
						$status = $ticket->find('.status', 0)->innertext;
						$r['id'] = substr($ticket->id, 6);
						$r['project'] = $ticket->find('.project', 0)->find('a', 0)->innertext;
						$r['status'] = $status;
						$r['type'] = $ticket->find('.tracker', 0)->innertext;
						$r['priority'] = $ticket->find('.priority', 0)->innertext;
						$r['subject'] = $ticket->find('.subject', 0)->find('a', 0)->innertext;
						$cl = $ticket->find('.done_ratio', 0)->find('.closed', 0);
						if($cl) $cl = $cl->getAttribute('style');
							else $cl = 'width: 0.00';
						$r['done'] = floatval(str_replace([';', '%'], '', substr($cl, 7)));
						$r['estimated'] = time::asTime(floatval($ticket->find('.estimated_hours', 0)->innertext));
						$r['spent'] = time::asTime(floatval($ticket->find('.spent_hours', 0)->innertext));
						$ret[] = $r;
					}
					$new_at_top = [];
					foreach ($ret as $k => $r) {
						switch ($r['status']) {
							case 'Новий':
							case 'Новый':
							case 'New':
							case 'У процесі':
							case 'В процессе':
							case 'In Progress':
							case 'Уточнити':
								unset($ret[$k]);
								$new_at_top[] = $r;
								break;
						}
					}
					usort($new_at_top, function($a, $b) {
						return $a['status'] == 'Новый' ? ($b['status'] == 'Новый' ? 0 : -1) : 1;
					});
					return array_merge($new_at_top, $ret);
				} else {
					return false;
				}
			} else return tickets::get($cookie);
		}

		public static function groupBy($tickets, $field, $values) {
			if(!$tickets) return $tickets;
			if(!is_array($values)) $values = [$values];
			$values = array_map(function($a) { return mb_strtolower((string) $a); }, $values);
			foreach($tickets as $k => $ticket) {
				if(!in_array(mb_strtolower((string) $ticket[$field]), $values)) unset($tickets[$k]);
			}
			return array_merge([], $tickets);
		}

		public static function idAsKey($tickets) {
			if(!$tickets) return $tickets;
			$ret = [];
			foreach($tickets as $ticket) {
				$ret[(int)$ticket['id']] = $ticket;
			}

			return $ret;
		}

		public static function getInfo($id) {
			$id = (int) $id;
			$c = cookie::get();
			$ans = Request::send('GET', $c['url'].'/issues/'.$id, ['Cookie', 'autologin='.urlencode($c['autologin'])]);
			if(!$ans) return self::getInfo($id);
			if(!strpos($ans, 'loggedas')) return self::error('login');
			$ans = str_get_html($ans);
			if(!$ans->find('.subject', 0)) return self::error('exist');
			$ret = [];
			$ret['title'] = $ans->find('title', 0)->plaintext;
			$ret['id'] = $id;
			$ret['subject'] = $ans->find('.subject', 0)->find('div', 0)->find('h3', 0)->plaintext;
			$att = $ans->find('.attributes', 0);
			$ret['status'] = $att->find('.status', 1)->plaintext;
			$ret['priority'] = $att->find('.priority', 1)->plaintext;
			$ret['start_date'] = $att->find('.start-date', 1)->plaintext;
			$ret['due_date'] = $att->find('.due-date', 1)->plaintext;
			$ret['assignee'] = $att->find('.assigned-to', 1)->plaintext;
			$ret['spent_time'] = time::asTime(floatval($att->find('.spent-time', 1)->plaintext));
			$est = $att->find('.estimated-hours', 1);
			$ret['estimated_hours'] = time::asTime($est ? floatval($est->plaintext) : 0);
			if($ret['estimated_hours'] == '00 год.') $ret['estimated_hours'] = 'Не вказано';
			$theme = $ans->find('#content', 0)->find('h2', 0)->plaintext;
			$theme = substr($theme, 0, strrpos($theme, '#'.$id) - 1);
			$ret['done'] = floatval($att->find('.progress', 1)->plaintext);
			$ret['type'] = $theme;
			$ret['history'] = '';
			$links = $ans->find('div.wiki', 0)->find('a');
			foreach ($links as $link) { 
				if(!preg_match('/^(?:https?:)?\/\/.*$/i', $link->getAttribute('href'))) $link->setAttribute('href', $c['url'].$link->getAttribute('href')); 
				$link->setAttribute('target', '_blank');
			}
			$ret['content'] = $ans->find('div.wiki', 0)->innertext;
			$history = $ans->find('#history', 0);
			if($history) $history = $history->find('.journal');
			if($history) {
				foreach($history as $story) {
					$story = $story->find('div', 0);
					$r = [];
					$d = $story->find('h4', 0);
					$r['user'] = $d->find('a', 1)->plaintext;
					$r['time'] = $d->find('a', 2)->getAttribute('title');
					$r['html'] = '';
					$details = $story->find('.details', 0);
					if($details) {
						$links = $details->find('a');
						foreach ($links as $link) { 
							$link->setAttribute('href', $c['url'].$link->getAttribute('href')); 
							$link->setAttribute('target', '_blank');
						}
						$r['html'] .= $details->innertext;
					}
					$details = $story->find('.wiki', 0);
					if($details && $details = $details->find('p', 0)) {
						$links = $details->find('a');
						foreach ($links as $link) { 
							$link->setAttribute('href', $c['url'].$link->getAttribute('href')); 
							$link->setAttribute('target', '_blank');
						}
						$r['html'] .= '<blockquote style="margin-top: 10px; background: #eaeaff">'.$details->innertext.'</blockquote>';
					}
					$ret['history'] = '<div class="history-entry"><div class="history-base tiny-size"><div class="changed-by fl-left"><b>'.$r['user'].'</b></div><div class="changed-time fl-right">'.$r['time'].'</div></div><div class="history-content">'.$r['html'].'</div></div>' . $ret['history'];
				}
			} else $ret['history'] = '';
			return $ret;
		}

		public static function error($errorMsg) {
			self::$error = $errorMsg;
			return false;
		}

		public static function getUpdateParams($id) {
			$id = (int) $id;
			$c = cookie::get();
			$ans = Request::send('GET', $c['url'].'/issues/'.$id, ['Cookie', 'autologin='.urlencode($c['autologin'])]);
			if(!$ans) return self::getUpdateParams($id);
			if(!strpos($ans, 'loggedas')) return self::error('login');
			$ans = str_get_html($ans);
			if(!$ans->find('.subject', 0)) return self::error('exist');
			$upd = $ans->find('#update', 0)->find('form', 0);
			$inputs = [];
			$i_html = $upd->find('input');
			foreach($i_html as $i) {
				if($i->getAttribute('type') != 'submit' && $i->getAttribute('type') != 'checkbox' && $i->getAttribute('value'))
					$inputs[$i->getAttribute('name')] = $i->getAttribute('value');
				elseif($i->getAttribute('type') == 'checkbox') 
					$inputs[$i->getAttribute('name')] = $i->getAttribute('checked') ? 1 : 0;
			}
			$i_html = $upd->find('select');
			foreach($i_html as $i) {
				$opt = $i->find('option[selected]', 0);
				if($opt) $opt = $opt->last_child() ? $opt->last_child : $opt;
					else $opt = $i->find('option', 0);
				$inputs[$i->getAttribute('name')] = $opt->getAttribute('value');
			}
			return $inputs;
		}

		public static function uploadTicket($id, $data, $session) {
			$id = (int) $id;
			$c = cookie::get();
			$ans = Request::send('POST', $c['url'].'/issues/'.$id, ['Cookie' => '_redmine_session='.$session], $data);
			if(!$ans || strpos($ans, 'login?back_url')) return false;
			return $ans;
		}
	}
?>