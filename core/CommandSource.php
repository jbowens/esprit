<?php

namespace esprit\core;

/**
 * Defines an interface for sources of Command objects.
 *
 * @author jbowens
 */
interface CommandSource {

    /**
     * Determines if a given command exists.
     *
     * @param String $commandName  the name of a command
     * @return true iff the command exists and is instantiable
     */
    public function isCommandDefined($commandName);

    /**
     * Takes a command name and returns an instance of that command.
     *
     * @param String $commandName  the name of a command
     * @return Command an instance of the command
     */
    public function instantiateCommand($commandName);

}
