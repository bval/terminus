<?php

namespace Pantheon\Terminus\Commands\Site;

use Consolidation\OutputFormatters\StructuredData\RowsOfFields;

class ListCommand extends SiteCommand
{
    /**
     * Displays the list of sites accessible to the currently logged-in user.
     *
     * @authorize
     *
     * @command site:list
     * @alias sites
     *
     * @field-labels
     *     name: Name
     *     id: ID
     *     service_level: Service Level
     *     framework: Framework
     *     owner: Owner
     *     created: Created
     *     memberships: Memberships
     * @return RowsOfFields
     *
     * @option team Team-only filter
     * @option owner Owner filter; "me" or user UUID
     * @option org Organization filter; "all" or organization UUID
     * @option name Name filter
     *
     * @usage terminus site:list
     *     Displays the list of all sites accessible to the currently logged-in user.
     * @usage terminus site:list --team
     *     Displays the list of sites of which the currently logged-in user is a member of the team.
     * @usage terminus site:list --owner=<user>
     *     Displays the list of accessible sites owned by the user with UUID <user>.
     * @usage terminus site:list --owner=me
     *     Displays the list of sites owned by the currently logged-in user.
     * @usage terminus site:list --org=<org>
     *     Displays a list of accessible sites associated with the <org> organization.
     * @usage terminus site:list --org=all
     *     Displays a list of accessible sites associated with any organization of which the currently logged-in is a member.
     * @usage terminus site:list --name=<regex>
     *     Displays a list of accessible sites with a name that matches <regex>.
     */
    public function index($options = ['team' => false, 'owner' => null, 'org' => null, 'name' => null,])
    {
        $this->sites()->fetch(
            [
                'org_id' => isset($options['org']) ? $options['org'] : null,
                'team_only' => isset($options['team']) ? $options['team'] : false,
            ]
        );

        if (isset($options['name']) && !is_null($name = $options['name'])) {
            $this->sites->filterByName($name);
        }
        if (isset($options['owner']) && !is_null($owner = $options['owner'])) {
            if ($owner == 'me') {
                $owner = $this->session()->getUser()->id;
            }
            $this->sites->filterByOwner($owner);
        }

        $sites = $this->sites->serialize();

        if (empty($sites)) {
            $this->log()->notice('You have no sites.');
        }

        return new RowsOfFields($sites);
    }
}
