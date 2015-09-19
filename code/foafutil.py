from rdflib.Graph import ConjunctiveGraph as Graph
from rdflib import Namespace, Literal, BNode, RDF, URIRef

# Return a string representing a FOAF RDF file
def generate_foaf_file(user):
    graph = Graph()
    FOAF = Namespace("http://xmlns.com/foaf/0.1/")
    RDFS = Namespace("http://www.w3.org/2000/01/rdf-schema#")
    # bind the namespaces
    graph.bind('foaf', FOAF)
    graph.bind('rdfs', RDFS)
    
    # add the user
    person = add_foaf_node(graph, FOAF, user)

    # add friends
    if user.get_profile().privacy_settings.display_public(user.get_profile().privacy_settings.display_friends):
        for friend in user.get_profile().get_friends():
            knows = add_foaf_node(graph, FOAF, friend)
            # add the seeAlso link
            graph.add((knows, RDFS['seeAlso'], URIRef(friend.get_profile().get_foaf_url())))
            # add the friend as someone this person knows
            graph.add((person, FOAF['knows'], knows))
    
    #print graph.serialize(format="pretty-xml")
    return graph.serialize(format="pretty-xml")

# Generate an individual FOAF node
def add_foaf_node(graph, FOAF, user):
    node = BNode()
    # add the user data
    graph.add((node, RDF.type, FOAF['Person']))
    graph.add((node, FOAF['nick'], Literal(user.username)))
    graph.add((node, FOAF['name'], Literal(user.get_profile().short_name())))
    graph.add((node, FOAF['firstName'], Literal(user.first_name)))
    graph.add((node, FOAF['img'], Namespace(user.get_profile().large_photo_url)))
    # add data based on privacy settings
    if user.get_profile().privacy_settings.display_public(user.get_profile().privacy_settings.display_email):
        graph.add((node, FOAF['mbox_sha1sum'], Literal(user.get_profile().mbox_sha1sum)))
    if user.get_profile().privacy_settings.display_public(user.get_profile().privacy_settings.display_age):
        graph.add((node, FOAF['dateOfBirth'], Literal(user.get_profile().birthday)))
    return node