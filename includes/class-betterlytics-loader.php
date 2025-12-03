<?php
/**
 * Register all actions and filters for the plugin.
 *
 * @package    Betterlytics
 * @subpackage Betterlytics/includes
 * @since      1.0.0
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API.
 *
 * @since 1.0.0
 */
class Betterlytics_Loader {

	/**
	 * The array of actions registered with WordPress.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array $actions The actions registered with WordPress.
	 */
	protected $actions;

	/**
	 * The array of filters registered with WordPress.
	 *
	 * @since  1.0.0
	 * @access protected
	 * @var    array $filters The filters registered with WordPress.
	 */
	protected $filters;

	/**
	 * Initialize the collections used to maintain the actions and filters.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		$this->actions = [];
		$this->filters = [];
	}

	/**
	 * Add a new action to the collection to be registered with WordPress.
	 *
	 * @since 1.0.0
	 * @param string $hook          The name of the WordPress action.
	 * @param object $component     A reference to the instance of the object.
	 * @param string $callback      The name of the function definition.
	 * @param int    $priority      Optional. The priority. Default is 10.
	 * @param int    $accepted_args Optional. The number of arguments. Default is 1.
	 */
	public function add_action( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->actions = $this->add( $this->actions, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * Add a new filter to the collection to be registered with WordPress.
	 *
	 * @since 1.0.0
	 * @param string $hook          The name of the WordPress filter.
	 * @param object $component     A reference to the instance of the object.
	 * @param string $callback      The name of the function definition.
	 * @param int    $priority      Optional. The priority. Default is 10.
	 * @param int    $accepted_args Optional. The number of arguments. Default is 1.
	 */
	public function add_filter( $hook, $component, $callback, $priority = 10, $accepted_args = 1 ) {
		$this->filters = $this->add( $this->filters, $hook, $component, $callback, $priority, $accepted_args );
	}

	/**
	 * A utility function that is used to register the actions and hooks.
	 *
	 * @since  1.0.0
	 * @access private
	 * @param  array  $hooks         The collection of hooks.
	 * @param  string $hook          The name of the WordPress filter.
	 * @param  object $component     A reference to the instance of the object.
	 * @param  string $callback      The name of the function definition.
	 * @param  int    $priority      The priority.
	 * @param  int    $accepted_args The number of arguments.
	 * @return array  The collection of hooks.
	 */
	private function add( $hooks, $hook, $component, $callback, $priority, $accepted_args ) {
		$hooks[] = [
			'hook'          => $hook,
			'component'     => $component,
			'callback'      => $callback,
			'priority'      => $priority,
			'accepted_args' => $accepted_args,
		];

		return $hooks;
	}

	/**
	 * Register the filters and actions with WordPress.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		foreach ( $this->filters as $hook ) {
			add_filter(
				$hook['hook'],
				[ $hook['component'], $hook['callback'] ],
				$hook['priority'],
				$hook['accepted_args']
			);
		}

		foreach ( $this->actions as $hook ) {
			add_action(
				$hook['hook'],
				[ $hook['component'], $hook['callback'] ],
				$hook['priority'],
				$hook['accepted_args']
			);
		}
	}
}
